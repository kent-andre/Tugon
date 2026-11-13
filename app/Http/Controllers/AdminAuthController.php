<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Support\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    // Checked when the email is unknown, so a wrong email takes as long as a wrong password.
    private const DUMMY_HASH = '$2y$12$LmmPMqQ.mgoEUbCx1.RriOKS/4iPK6YBzGes/rC1RPvF0nvMPSzfm';

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower(trim($data['email']));
        $admin = Admin::where('email', $email)->first();
        $passwordOk = Hash::check($data['password'], $admin?->password ?? self::DUMMY_HASH);

        if (! $admin || ! $passwordOk) {
            AuditLog::record("Failed login for official account {$email}", 'Accounts');

            return $this->fail($email, 'Incorrect email or password.');
        }

        if (! $admin->is_active) {
            return $this->fail($email, 'This account is disabled. Please contact the Punong Barangay.');
        }

        $request->session()->regenerate(); // new session id after login
        session(['admin_logged_in' => true, 'admin' => $admin->sessionData()]);
        $admin->forceFill(['last_login_at' => now()])->save();
        AuditLog::record('Logged in', 'Accounts');

        return redirect(session()->pull('admin_intended_url', '/admin/dashboard'));
    }

    public function logout(Request $request)
    {
        if (session('admin_logged_in')) { AuditLog::record('Logged out', 'Accounts'); }
        $request->session()->forget(['admin_logged_in', 'admin', 'admin_intended_url']);
        $request->session()->regenerate();

        return redirect('/admin-login');
    }

    private function fail(string $email, string $message)
    {
        return back()
            ->withInput(['email' => $email])
            ->withErrors(['login' => $message])
            ->with('role', 'admin');
    }
}
