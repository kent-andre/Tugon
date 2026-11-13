<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\Event;
use App\Support\Current;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DocumentRequestController extends Controller
{
    public function create(Request $request)
    {
        // Coming from a Health event on the calendar (?event=ID): pre-fill the vaccination form.
        $preEvent = null;
        if ($request->filled('event')) {
            $e = Event::find((int) $request->query('event'));
            $preEvent = $e && $e->canPreregister() ? $e : null;
        }

        return view('resident.request-document', [
            'docTypes' => config('pabarang.doc_types'),
            'preEvent' => $preEvent,
        ]);
    }

    /** POST /request-document/{type} - one route for all 6 forms. */
    public function store(Request $request, string $type)
    {
        $types = config('pabarang.doc_types');
        abort_unless(isset($types[$type]), 404);
        $residentId = Current::residentId();
        abort_unless($residentId, 403);

        // Errors go to a bag named after the type, so only that modal shows them.
        $v = $request->validateWithBag($type, $this->rules($type, $types[$type]), [], $this->attributeNames());

        [$summary, $details] = $this->describe($type, $v);

        $files = [];
        foreach (['valid_id' => 'Valid ID', 'medical_certificate' => 'Medical certificate'] as $field => $label) {
            if ($request->hasFile($field)) {
                $f = $request->file($field);
                $files[] = [
                    'label' => $label,
                    'name'  => $f->getClientOriginalName(),
                    'path'  => $f->store("document-requests/{$residentId}", 'local'),   // private disk
                ];
            }
        }

        $doc = DB::transaction(function () use ($residentId, $type, $summary, $details, $files) {
            $doc = DocumentRequest::create([
                'ref'         => DocumentRequest::nextRef(),
                'resident_id' => $residentId,
                'type'        => $type,
                'status'      => 'pending',
                'summary'     => $summary,
                'details'     => $details,
                'files'       => $files,
            ]);
            $doc->logs()->create(['status' => 'pending']);
            return $doc;
        });

        return redirect('/my-reports')->with('filed_doc', ['ref' => $doc->ref, 'name' => $doc->typeName()]);
    }

    private function rules(string $type, array $cfg): array
    {
        $validId = ['required', 'image', 'max:5120'];
        $purpose = [
            'purpose'       => ['required', Rule::in($cfg['purposes'] ?? [])],
            'purpose_other' => ['nullable', 'required_if:purpose,Other', 'string', 'max:120'],
        ];

        return match ($type) {
            'clearance' => $purpose + [
                'copies'   => 'required|integer|min:1|max:10',
                'valid_id' => $validId,
            ],
            'residency' => $purpose + [
                'years'    => 'required|integer|min:0|max:120',
                'valid_id' => $validId,
            ],
            'indigency' => $purpose + [
                'valid_id' => $validId,
            ],
            'business' => [
                'business_name'    => 'required|string|max:120',
                'business_address' => 'required|string|max:200',
                'business_type'    => 'required|string|max:80',
                'valid_id'         => $validId,
            ],
            'vaccination' => [
                'vaccine_for'    => 'required|in:self,dependent',
                'dependent_name' => 'nullable|required_if:vaccine_for,dependent|string|max:120',
                'dependent_age'  => 'nullable|required_if:vaccine_for,dependent|integer|min:0|max:120',
                'vaccine'        => ['required', Rule::in($cfg['vaccines'])],
                'vaccine_other'  => 'nullable|required_if:vaccine,Other|string|max:80',
                'preferred_date' => 'required|date|after_or_equal:today',
                'event_id'       => 'nullable|integer',
            ],
            'id' => [
                'id_type'             => 'required|in:senior,pwd',
                'full_name'           => 'required|string|max:120',
                'birthdate'           => 'required|date|before:today',
                'disability'          => ['nullable', 'required_if:id_type,pwd', Rule::in($cfg['disabilities'])],
                'disability_other'    => 'nullable|string|max:80',
                'medical_certificate' => 'nullable|required_if:id_type,pwd|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
                'valid_id'            => $validId,
            ],
        };
    }

    /** [summary line, details shown to the admin as label => value] */
    private function describe(string $type, array $v): array
    {
        $purpose = ($v['purpose'] ?? '') === 'Other' ? 'Other: ' . $v['purpose_other'] : ($v['purpose'] ?? '');
        $vaccine = ($v['vaccine'] ?? '') === 'Other' ? 'Other: ' . trim($v['vaccine_other'] ?? '') : ($v['vaccine'] ?? '');
        $disability = ($v['disability'] ?? '') === 'Other' && trim($v['disability_other'] ?? '') !== ''
            ? 'Other: ' . trim($v['disability_other']) : ($v['disability'] ?? null);

        return match ($type) {
            'clearance' => [$purpose, ['Purpose' => $purpose, 'Copies' => (string) $v['copies']]],
            'residency' => [$purpose, ['Purpose' => $purpose, 'Years of residency' => (string) $v['years']]],
            'indigency' => [$purpose, ['Purpose' => $purpose]],
            'business'  => [$v['business_name'], [
                'Business name'    => $v['business_name'],
                'Business address' => $v['business_address'],
                'Type of business' => $v['business_type'],
            ]],
            'vaccination' => [
                $vaccine . ', ' . Carbon::parse($v['preferred_date'])->format('M j'),
                array_filter([
                    'Event'          => $this->eventTitle($v['event_id'] ?? null),
                    'For'            => $v['vaccine_for'] === 'self' ? 'Myself' : 'Dependent',
                    'Dependent'      => $v['vaccine_for'] === 'dependent' ? $v['dependent_name'] . ' (age ' . $v['dependent_age'] . ')' : null,
                    'Vaccine'        => $vaccine,
                    'Preferred date' => Carbon::parse($v['preferred_date'])->format('M j, Y'),
                ]),
            ],
            'id' => [
                $v['id_type'] === 'pwd' ? 'PWD ID' : 'Senior Citizen ID',
                array_filter([
                    'ID type'    => $v['id_type'] === 'pwd' ? 'PWD ID' : 'Senior Citizen ID',
                    'Full name'  => $v['full_name'],
                    'Birthdate'  => Carbon::parse($v['birthdate'])->format('M j, Y'),
                    'Disability' => $v['id_type'] === 'pwd' ? $disability : null,
                ]),
            ],
        };
    }

    /** Title of the calendar event a vaccination request was made from, if it still allows it. */
    private function eventTitle($id): ?string
    {
        if (! $id) {
            return null;
        }
        $e = Event::find((int) $id);
        return $e && $e->canPreregister() ? $e->title . ' (' . $e->starts_at->format('M j') . ')' : null;
    }

    private function attributeNames(): array
    {
        return [
            'purpose_other'       => 'reason',
            'vaccine_other'       => 'vaccine name',
            'disability_other'    => 'type of disability',
            'valid_id'            => 'valid ID',
            'vaccine_for'         => '"this vaccination is for"',
            'dependent_name'      => "dependent's name",
            'dependent_age'       => "dependent's age",
            'preferred_date'      => 'preferred date',
            'id_type'             => 'ID type',
            'medical_certificate' => 'medical certificate',
        ];
    }
}
