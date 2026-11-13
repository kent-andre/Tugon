<?php

namespace App\Http\Controllers;

use App\Mail\KpNotice;
use App\Support\AuditLog;
use App\Support\BlotterStore;
use App\Support\ConcernStore;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class BlotterController extends Controller
{
    // Cases that are still being mediated.
    private const OPEN = ['filed', 'summons', 'hearing'];

    // Offices a case can be referred to instead of mediation.
    private const REFERRALS = [
        'pnp'  => 'PNP / Prosecutor',
        'vawc' => 'Women and Children Protection Desk',
        'bcpc' => 'BCPC / MSWDO',
    ];

    // Same list as the "Type of incident" dropdown in the Convert to Blotter form.
    private const TYPES = [
        'Boundary Dispute', 'Verbal Altercation', 'Physical Injury', 'Property Damage', 'Theft',
        'Threats / Harassment', 'Debt / Financial Dispute', 'Noise Disturbance',
        'Family / Domestic Dispute', 'Other',
    ];

    // How the officer served the summons (Officer's Return).
    private const SERVICE_METHODS = [
        'personal'  => 'Handed to the respondent personally',
        'residence' => "Left at the respondent's residence with a person of suitable age",
        'refused'   => 'Respondent refused to receive it',
    ];

    // Text written to the audit log for each button in the modal.
    private const ACTION_LOG = [
        'summons'  => 'summons issued',
        'return'   => "officer's return recorded",
        'schedule' => 'hearing scheduled',
        'settle'   => 'marked settled',
        'escalate' => 'escalated, CFA issued',
        'dismiss'  => 'dismissed',
        'reopen'   => 'opened for mediation',
    ];

    public function index()
    {
        $blotters = collect(BlotterStore::all())
            ->map(fn ($b) => $this->present($b))
            ->values()
            ->all();

        return view('admin.blotters', [
            'blotters' => $blotters,
            'methods'  => self::SERVICE_METHODS,
        ]);
    }

    // Convert to Blotter button in the admin Concerns page. The officer types the
    // entry from the complainant's statement; the concern only supplies the zone
    // and the source tracking code.
    public function storeFromConcern(Request $request, string $code)
    {
        $concern = ConcernStore::find($code);
        abort_if($concern === null, 404);

        $anonymous = ! empty($concern['is_anonymous']) || empty($concern['reporter_name']);
        abort_if($anonymous, 422, 'Anonymous reports cannot be recorded in the blotter. A blotter entry needs a named complainant who signs it.');
        abort_unless(empty($concern['blotter_ref']), 422, 'This concern is already recorded in the blotter.');
        abort_if(($concern['status'] ?? '') === 'resolved', 422, 'This concern is already resolved.');

        $data = $request->validate([
            'action'            => ['required', Rule::in(['mediation', 'record', 'pnp', 'vawc', 'bcpc'])],
            'type'              => ['required', Rule::in(self::TYPES)],
            'complainant'       => ['required', 'string', 'max:255'],
            'complainant_info'  => ['nullable', 'string', 'max:500'],
            'complainant_email' => ['nullable', 'email', 'max:255'],
            'respondent'        => ['required', 'string', 'max:255'],
            'respondent_info'   => ['nullable', 'string', 'max:500'],
            'respondent_email'  => ['nullable', 'email', 'max:255'],
            'narrative'         => ['required', 'string', 'max:3000'],
            'incident'          => ['required', 'string', 'max:100'],
            'place'             => ['required', 'string', 'max:255'],
            'witnesses'         => ['nullable', 'string', 'max:500'],
            'recorded_by'       => ['required', 'string', 'max:255'],
        ]);

        // The server decides the starting status from the action taken.
        if ($data['action'] === 'mediation') {
            $status = 'filed';
            $referredTo = 'Lupon Tagapamayapa';
        } elseif ($data['action'] === 'record') {
            $status = 'recorded';
            $referredTo = '';
        } else {
            $status = 'referred';
            $referredTo = self::REFERRALS[$data['action']];
        }

        [$ref, $updated] = DB::transaction(function () use ($data, $concern, $code, $status, $referredTo) {
            $blotter = BlotterStore::create([
                'type'                => $data['type'],
                'zone'                => $concern['purok'],
                'complainant'         => $data['complainant'],
                'complainant_info'    => $data['complainant_info'] ?? '',
                'complainant_email'   => $data['complainant_email'] ?? null,
                'respondent'          => $data['respondent'],
                'respondent_info'     => $data['respondent_info'] ?? '',
                'respondent_email'    => $data['respondent_email'] ?? null,
                'narrative'           => $data['narrative'],
                'incident'            => $data['incident'],
                'place'               => $data['place'],
                'witnesses'           => $data['witnesses'] ?? '',
                'recorded_by'         => $data['recorded_by'],
                'referred_to'         => $referredTo,
                'source_concern_code' => $code,
                'status'              => $status,
            ]);

            $ref = $this->refFor($blotter['id']);
            $updated = ConcernStore::update($code, ['status' => 'blotter', 'blotter_ref' => $ref]);
            AuditLog::record("Recorded {$ref} from concern {$code}", 'Blotter');

            return [$ref, $updated];
        });

        // Only the fields the Concerns page redraws its card from.
        return response()->json([
            'ok'          => true,
            'blotter_ref' => $ref,
            'concern'     => [
                'status'      => $updated['status'],
                'verify'      => $updated['verification'] ?? 'unverified',
                'flag_reason' => $updated['flag_reason'] ?? '',
                'assigned'    => $updated['assigned_to'] ?? '',
                'internal'    => $updated['internal_notes'] ?? '',
                'public'      => $updated['public_update'] ?? '',
                'blotter'     => $updated['blotter_ref'] ?? '',
            ],
        ], 201);
    }

    // One endpoint for every button in the modal. The server decides the new status.
    public function update(Request $request, string $blotter)
    {
        $current = BlotterStore::find((int) $blotter);
        abort_if($current === null, 404);
        $id = (int) $current['id'];

        $action = $request->validate([
            'action' => ['required', Rule::in(array_keys(self::ACTION_LOG))],
        ])['action'];

        $isOpen = in_array($current['status'], self::OPEN, true);
        $logNote = '';
        $sent = [];

        switch ($action) {
            case 'summons':
                abort_unless($current['status'] === 'filed', 422, 'Summons can only be issued for a newly filed case.');
                abort_if($this->respondentUnknown($current), 422, "Enter the respondent's name before issuing a summons.");
                $data = $request->validate([
                    'hearing_at'    => ['required', 'date', 'after:now'],
                    'hearing_place' => ['required', 'string', 'max:255'],
                ]);
                $this->checkOfficeHours($data['hearing_at']);

                // Send first. If an email fails, nothing is saved.
                $sent = $this->sendNotices($current, $this->noticeData($current, $data['hearing_at'], $data['hearing_place']));

                $updated = BlotterStore::update($id, [
                    'status'          => 'summons',
                    'hearing_at'      => $data['hearing_at'],
                    'hearing_place'   => $data['hearing_place'],
                    'mediator'        => config('pabarang.captain_name'),
                    'summons_sent_at' => now(),
                ]);
                $logNote = $sent ? ', emailed to ' . implode(' and ', $sent) : ', no email on file';
                break;

            case 'return':
                abort_unless(in_array($current['status'], ['summons', 'hearing'], true), 422, "The officer's return is recorded after the summons is issued.");
                $data = $request->validate([
                    'served_by'      => ['required', 'string', 'max:120'],
                    'served_at'      => ['required', 'date', 'before_or_equal:now'],
                    'service_method' => ['required', Rule::in(array_keys(self::SERVICE_METHODS))],
                ]);
                $updated = BlotterStore::update($id, $data);
                break;

            case 'schedule':
                abort_unless($isOpen, 422, 'This case is no longer open for mediation.');
                $data = $request->validate([
                    'hearing_at' => ['required', 'date'],
                    'mediator'   => ['required', 'string', 'max:120'],
                ]);
                $this->checkOfficeHours($data['hearing_at']);
                $updated = BlotterStore::update($id, [
                    'status'     => 'hearing',
                    'hearing_at' => $data['hearing_at'],
                    'mediator'   => $data['mediator'],
                ]);
                break;

            case 'settle':
                abort_unless($isOpen, 422, 'This case is no longer open for mediation.');
                $data = $request->validate(['settlement' => ['required', 'string', 'max:2000']]);
                $updated = BlotterStore::update($id, ['status' => 'settled', 'settlement' => $data['settlement']]);
                break;

            case 'escalate':
                abort_unless($isOpen, 422, 'This case is no longer open for mediation.');
                $year = now()->year;
                $updated = BlotterStore::update($id, [
                    'status' => 'escalated',
                    'cfa_no' => sprintf('CFA-%d-%04d', $year, BlotterStore::cfaCount($year) + 1),
                ]);
                break;

            case 'dismiss':
                abort_unless($isOpen, 422, 'This case is no longer open for mediation.');
                $data = $request->validate(['settlement' => ['nullable', 'string', 'max:2000']]);
                $updated = BlotterStore::update($id, [
                    'status'     => 'dismissed',
                    'settlement' => $data['settlement'] ?? 'Case dismissed by the Lupon Chairman.',
                ]);
                break;

            case 'reopen':
                abort_unless($current['status'] === 'recorded', 422, 'Only recorded entries can be opened for mediation.');
                $updated = BlotterStore::update($id, ['status' => 'filed']);
                break;
        }

        AuditLog::record($this->refFor($id) . ' ' . self::ACTION_LOG[$action] . $logNote, 'Blotter');

        return response()->json(['ok' => true, 'blotter' => $this->present($updated), 'sent' => $sent]);
    }

    // Printable summons (for the respondent, with the Officer's Return) and notice of hearing.
    public function printSummons(string $blotter)
    {
        $b = BlotterStore::find((int) $blotter);
        abort_if($b === null, 404);
        abort_if(empty($b['hearing_at']), 422, 'Issue the summons first so the hearing date is set.');

        return view('admin.summons-print', [
            'case'    => $this->noticeData($b, $b['hearing_at'], $b['hearing_place'] ?? null),
            'b'       => $this->present($b),
            'methods' => self::SERVICE_METHODS,
        ]);
    }

    // Sends the Summons to the respondent and the Notice of Hearing to the complainant,
    // only to the emails on file. Stops the request if any email fails.
    private function sendNotices(array $b, array $case): array
    {
        $sent = [];

        try {
            if (! empty($b['respondent_email'])) {
                Mail::to($b['respondent_email'])->send(new KpNotice('summons', $case));
                $sent[] = 'respondent';
            }
            if (! empty($b['complainant_email'])) {
                Mail::to($b['complainant_email'])->send(new KpNotice('notice', $case));
                $sent[] = 'complainant';
            }
        } catch (\Throwable $e) {
            report($e);
            abort(502, 'The email could not be sent, so the summons was not issued. Check the internet connection and the mail settings, then try again.');
        }

        return $sent;
    }

    // Case details for the summons and notice. No narrative and no phone numbers.
    private function noticeData(array $b, string $hearingAt, ?string $place): array
    {
        $at = Carbon::parse($hearingAt);
        $issued = ! empty($b['summons_sent_at']) ? Carbon::parse($b['summons_sent_at']) : now();

        return [
            'ref'          => $this->refFor((int) $b['id']),
            'type'         => $b['type'],
            'complainant'  => $b['complainant'],
            'respondent'   => $b['respondent'],
            'hearing_date' => $at->format('l, F j, Y'),
            'hearing_time' => $at->format('g:i A'),
            'place'        => $place ?: config('pabarang.hearing_place'),
            'issued'       => $issued->format('F j, Y'),
            'barangay'     => config('pabarang.barangay_name'),
            'address'      => config('pabarang.barangay_address'),
            'captain'      => config('pabarang.captain_name'),
        ];
    }

    // Hearings are held at the barangay hall during office hours only.
    private function checkOfficeHours(string $value): void
    {
        $at = Carbon::parse($value);
        $minutes = $at->hour * 60 + $at->minute;

        abort_if(
            $at->isWeekend() || $minutes < 8 * 60 || $minutes > 17 * 60,
            422,
            'Hearings can only be set on weekdays, from 8:00 AM to 5:00 PM.'
        );
    }

    private function respondentUnknown(array $b): bool
    {
        $name = strtolower(trim($b['respondent'] ?? ''));

        return $name === '' || str_starts_with($name, 'unknown');
    }

    private function refFor(int $id): string
    {
        return 'BR-' . str_pad((string) $id, 5, '0', STR_PAD_LEFT);
    }

    // Shapes a blotter for the admin view.
    private function present(array $b): array
    {
        $fmt = fn ($v, $f = 'M j, Y, g:i A') => ! empty($v) ? date($f, strtotime($v)) : '';

        return [
            'id'                 => $b['id'],
            'ref'                => $this->refFor((int) $b['id']),
            'type'               => $b['type'] ?? '',
            'zone'               => $b['zone'] ?? '',
            'complainant'        => $b['complainant'] ?? '',
            'complainant_info'   => $b['complainant_info'] ?? '',
            'complainant_email'  => $b['complainant_email'] ?? '',
            'respondent'         => $b['respondent'] ?? '',
            'respondent_info'    => $b['respondent_info'] ?? '',
            'respondent_email'   => $b['respondent_email'] ?? '',
            'respondent_unknown' => $this->respondentUnknown($b),
            'narrative'          => $b['narrative'] ?? '',
            'incident'           => $b['incident'] ?? '',
            'place'              => $b['place'] ?? '',
            'witnesses'          => $b['witnesses'] ?? '',
            'recorded_by'        => $b['recorded_by'] ?? '',
            'referred_to'        => $b['referred_to'] ?? '',
            'source'             => $b['source_concern_code'] ?? '',
            'filed'              => date('M j, Y', strtotime($b['created_at'])),
            'status'             => $b['status'],
            'hearing_date'       => $fmt($b['hearing_at'] ?? null),
            'hearing_place'      => $b['hearing_place'] ?? '',
            'mediator'           => $b['mediator'] ?? '',
            'settlement'         => $b['settlement'] ?? '',
            'cfa_no'             => $b['cfa_no'] ?? '',
            'summons_sent'       => $fmt($b['summons_sent_at'] ?? null),
            'served_by'          => $b['served_by'] ?? '',
            'served_at'          => $fmt($b['served_at'] ?? null),
            'served_at_input'    => $fmt($b['served_at'] ?? null, 'Y-m-d\TH:i'),
            'service_method'     => $b['service_method'] ?? '',
            'service_label'      => self::SERVICE_METHODS[$b['service_method'] ?? ''] ?? '',
        ];
    }
}
