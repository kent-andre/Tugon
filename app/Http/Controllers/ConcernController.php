<?php

namespace App\Http\Controllers;

use App\Jobs\CheckConcernPhoto;
use App\Models\Concern;
use App\Models\Resident;
use App\Support\ConcernStore;
use App\Support\EvidenceFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

// Saves a report sent from the landing page. It shows up in the admin Concerns
// page and can be found on Check Status with its tracking code.
class ConcernController extends Controller
{
    public function store(Request $request)
    {
        // Honeypot: real users never fill this hidden field
        if ($request->filled('website')) {
            return response()->json(['message' => 'Rejected.'], 422);
        }

        $anonymous = $request->boolean('anonymous');
        $signedIn = (bool) session('resident_logged_in');

        $data = $request->validate([
            'category'      => ['required', 'in:Crime/Safety,Environment,Infrastructure,Water Supply,Streetlights,Noise,Other'],
            'description'   => ['required', 'string', 'max:1000'],
            'purok'         => ['required', 'in:Purok 1,Purok 2,Purok 3,Purok 4,Purok 5'],
            'reporter_name' => ['nullable', 'string', 'max:255'],
            // Optional for anonymous reports (officials can only follow up if one is given)
            // and for signed-in residents (taken from their account below).
            'contact'       => [Rule::requiredIf(! $anonymous && ! $signedIn), 'nullable', 'string', 'max:255'],
            'latitude'      => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'     => ['nullable', 'numeric', 'between:-180,180'],
            // How sure the phone was, in meters, and whether the resident moved the pin by hand
            'location_accuracy' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'location_adjusted' => ['nullable', 'boolean'],
            // Listed types only: no SVG (it can carry scripts)
            'evidence'      => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'data_consent'  => ['accepted'],
        ], [
            'contact.required' => 'Please give a contact number or email, or tick Anonymous.',
            'evidence.mimes'   => 'The photo must be a JPG, PNG or WebP image.',
        ]);

        // A signed-in resident's named report is linked to their account (shown in My Reports).
        // Anonymous reports are never linked, even when signed in.
        $residentId = (! $anonymous && $signedIn) ? session('resident.id') : null;
        $reporterName = $anonymous ? null : ($data['reporter_name'] ?? ($residentId ? session('resident.full_name') : null));

        // The resident Submit Concern page has no contact field, so a named report
        // uses the contact saved on the resident's account.
        $contact = trim((string) ($data['contact'] ?? ''));
        if ($contact === '' && $residentId) {
            $contact = $this->accountContact((int) $residentId);
        }

        // Photo goes to private storage (storage/app/private/evidence), not reachable by link.
        // Officials see it through /admin/concerns/{code}/photo.
        $evidencePath = $request->file('evidence')->store('evidence', EvidenceFile::DISK);

        try {
            $concern = ConcernStore::create([
                'tracking_code' => $this->makeCode(),
                'category'      => $data['category'],
                'description'   => $data['description'],
                'purok'         => $data['purok'],
                'reporter_name' => $reporterName,
                'contact'       => $contact,
                'is_anonymous'  => $anonymous,
                'latitude'      => $data['latitude'] ?? null,
                'longitude'     => $data['longitude'] ?? null,
                'evidence_path' => $evidencePath,
                'resident_id'   => $residentId,
            ]);
        } catch (Throwable $e) {
            // Don't leave a photo on disk with no report
            Storage::disk(EvidenceFile::DISK)->delete($evidencePath);
            throw $e;
        }

        // Location accuracy is saved separately, so ConcernStore stays as it is.
        // Skipped until the migration has added the columns.
        if (! empty($data['latitude']) && Schema::hasColumn('concerns', 'location_accuracy')) {
            Concern::where('tracking_code', $concern['tracking_code'])->update([
                'location_accuracy' => isset($data['location_accuracy']) ? (int) round($data['location_accuracy']) : null,
                'location_adjusted' => (bool) ($data['location_adjusted'] ?? false),
            ]);
        }

        // Flags suspicious photos for admin review after the response is sent; never rejects
        CheckConcernPhoto::dispatchAfterResponse($concern['tracking_code']);

        return response()->json(['tracking_code' => $concern['tracking_code']], 201);
    }

    // First contact column found on the resident's account ('' if none).
    private function accountContact(int $residentId): string
    {
        $resident = Resident::find($residentId);
        if (! $resident) {
            return '';
        }
        $attrs = $resident->getAttributes();
        foreach (['contact_number', 'contact', 'mobile', 'phone', 'email'] as $col) {
            if (! empty($attrs[$col])) {
                return mb_substr((string) $attrs[$col], 0, 255);
            }
        }

        return '';
    }

    // Tracking code like PB-2026-K7M2XQ (year + 6 characters). Letters that look
    // alike (O/0, I/1) are left out so it is easy to read out or type.
    private function makeCode(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $suffix = '';
            for ($i = 0; $i < 6; $i++) {
                $suffix .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $code = 'PB-' . now()->year . '-' . $suffix;
        } while (ConcernStore::codeExists($code));

        return $code;
    }
}
