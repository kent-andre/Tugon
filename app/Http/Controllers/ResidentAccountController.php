<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Support\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// Admin Resident Accounts page: review registrations, approve or reject them.
class ResidentAccountController extends Controller
{
    public function index()
    {
        $residents = Resident::orderByRaw("status = 'pending' desc")
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Resident $r) => $this->present($r))
            ->all();

        return view('admin.accounts', ['residents' => $residents]);
    }

    public function approve(Resident $resident)
    {
        abort_unless($resident->status === 'pending', 422, 'Only pending registrations can be approved.');

        // Resident ID like ALJ-2026-0001, numbered per year.
        $no = DB::transaction(function () use ($resident) {
            $year = now()->year;
            $last = Resident::where('resident_no', 'like', "ALJ-{$year}-%")->lockForUpdate()->max('resident_no');
            $next = $last ? ((int) substr($last, -4)) + 1 : 1;
            $no = sprintf('ALJ-%d-%04d', $year, $next);

            $resident->update(['status' => 'approved', 'resident_no' => $no, 'approved_at' => now(), 'reject_reason' => null]);

            return $no;
        });

        AuditLog::record("Approved resident account {$no}", 'Accounts');

        return response()->json(['ok' => true, 'resident_id' => $no]);
    }

    public function reject(Request $request, Resident $resident)
    {
        abort_unless($resident->status === 'pending', 422, 'Only pending registrations can be rejected.');
        $data = $request->validate(['reason' => ['required', 'string', 'max:300']]);

        $resident->update(['status' => 'rejected', 'reject_reason' => $data['reason']]);
        AuditLog::record("Rejected resident registration #{$resident->id}", 'Accounts');

        return response()->json(['ok' => true]);
    }

    // The ID photo is private, so it is only sent to a logged-in official.
    public function idPhoto(Resident $resident)
    {
        abort_unless($resident->valid_id_path && Storage::disk('local')->exists($resident->valid_id_path), 404);

        return Storage::disk('local')->response($resident->valid_id_path);
    }

    // Keys match what the Resident Accounts page reads.
    private function present(Resident $r): array
    {
        return [
            'id'             => $r->id,
            'name'           => trim("{$r->first_name} {$r->last_name}"),
            'fullName'       => $r->fullName(),
            'status'         => $r->status,
            'zone'           => $r->purok ?? '',
            'contact'        => $r->contact ?? '',
            'registered'     => $r->created_at->format('M j, Y'),
            'residentId'     => $r->resident_no,
            'approvedOn'     => $r->approved_at?->format('M j, Y'),
            'birthdateLabel' => $r->birthdate?->format('F j, Y') ?? 'Not provided',
            'age'            => $r->birthdate?->age ?? '',
            'gender'         => $r->gender ?? '',
            'civil_status'   => $r->civil_status ?? '',
            'occupation'     => $r->occupation,
            'email'          => $r->email,
            'emergency'      => $r->emergency_contact,
            'address'        => $r->address ?? '',
            'idType'         => $r->id_type ?: 'Valid ID',
            'idUrl'          => $r->valid_id_path ? url("/admin/accounts/{$r->id}/id") : '',
            'reason'         => $r->reject_reason ?? '',
        ];
    }
}
