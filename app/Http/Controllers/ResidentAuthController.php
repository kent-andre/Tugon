<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Support\AuditLog;
use App\Support\StrongPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ResidentAuthController extends Controller
{
    private const DUMMY_HASH = '$2y$12$LmmPMqQ.mgoEUbCx1.RriOKS/4iPK6YBzGes/rC1RPvF0nvMPSzfm';

    public const PUROKS = ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'];

    public const ID_TYPES = [
        'PhilSys National ID', "Driver's License", 'Passport', 'UMID', 'Postal ID',
        "Voter's ID or Certification", 'Senior Citizen ID', 'PWD ID', 'Student ID', 'Barangay ID',
    ];

    // Create Account form. The account stays pending until an official approves it.
    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name'        => ['required', 'string', 'max:100'],
            'middle_name'       => ['nullable', 'string', 'max:100'],
            'last_name'         => ['required', 'string', 'max:100'],
            'birthdate'         => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'gender'            => ['required', Rule::in(['Male', 'Female', 'Prefer not to say'])],
            'civil_status'      => ['required', Rule::in(['Single', 'Married', 'Widowed', 'Separated'])],
            'occupation'        => ['nullable', 'string', 'max:100'],
            'email'             => ['required', 'email', 'max:255', Rule::unique('residents', 'email')],
            'mobile_number'     => ['required', 'regex:/^09\d{9}$/'],
            'purok'             => ['required', Rule::in(self::PUROKS)],
            'address'           => ['required', 'string', 'max:255'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            // Strong password, and not made from the person's own name or email
            'password'          => StrongPassword::rules([$request->input('first_name'), $request->input('last_name'), $request->input('email')]),
            'id_type'           => ['required', Rule::in(self::ID_TYPES)],
            'valid_id'          => ['required', 'image', 'max:5120'],
            'data_consent'      => ['accepted'],
        ], [
            'email.unique'        => 'An account with this email already exists.',
            'mobile_number.regex' => 'Enter the mobile number as 09XXXXXXXXX.',
        ] + StrongPassword::messages());

        // IDs are private: saved in storage/app/private, never in the public folder.
        $idPath = $request->file('valid_id')->store('valid-ids', 'local');

        $resident = Resident::create([
            'first_name'        => $data['first_name'],
            'middle_name'       => $data['middle_name'] ?? null,
            'last_name'         => $data['last_name'],
            'birthdate'         => $data['birthdate'],
            'gender'            => $data['gender'],
            'civil_status'      => $data['civil_status'],
            'occupation'        => $data['occupation'] ?? null,
            'email'             => strtolower(trim($data['email'])),
            'password'          => $data['password'],
            'purok'             => $data['purok'],
            'address'           => $data['address'],
            'contact'           => $data['mobile_number'],
            'emergency_contact' => $data['emergency_contact'] ?? null,
            'id_type'           => $data['id_type'],
            'valid_id_path'     => $idPath,
            'status'            => 'pending',
        ]);

        // Audit log is for officials only: AuditLog::record("New resident registration #{$resident->id}", 'Accounts');

        return redirect('/register')->with('registered', true);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $resident = Resident::where('email', strtolower(trim($data['email'])))->first();
        $passwordOk = Hash::check($data['password'], $resident?->password ?? self::DUMMY_HASH);

        if (! $resident || ! $passwordOk) {
            // Audit log is for officials only: AuditLog::record('Failed login for a resident account', 'Accounts');

            return $this->fail($request, 'Incorrect email or password.');
        }

        if ($resident->status === 'pending') {
            return $this->fail($request, 'Your account is still waiting for barangay approval.');
        }

        if ($resident->status === 'rejected') {
            return $this->fail($request, 'Your registration was not approved: ' . $resident->reject_reason . ' Please visit the barangay hall.');
        }

        $intended = $request->session()->pull('intended_url');
        $request->session()->regenerate();

        $request->session()->put('resident_logged_in', true);
        $request->session()->put('resident', [
            'id'         => $resident->id,
            'email'      => $resident->email,
            'first_name' => $resident->first_name,
            'full_name'  => trim("{$resident->first_name} {$resident->last_name}"),
            'purok'      => $resident->purok,
        ]);

        return redirect($intended ?? '/home');
    }

    private function fail(Request $request, string $message)
    {
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['login' => $message])
            ->with('role', 'resident');
    }
}
