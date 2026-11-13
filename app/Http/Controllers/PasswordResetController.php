<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Support\StrongPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

// Forgot password for residents. Officials reset theirs through Official Accounts
// (captain) or Change Password.
class PasswordResetController extends Controller
{
    private const EXPIRES_MINUTES = 60;

    public function request()
    {
        return view('auth.forgot-password');
    }

    // Sends the link. The answer is the same whether or not the email has an
    // account, so the form can't be used to find out who is registered.
    public function send(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255']]);
        $email = strtolower(trim($data['email']));

        $resident = Resident::where('email', $email)->first();
        $recent = DB::table('resident_password_resets')->where('email', $email)
            ->where('created_at', '>', now()->subMinute())->exists();

        // One email per minute per address, so the form can't flood someone's inbox
        if ($resident && ! $recent) {
            $token = Str::random(64);
            DB::table('resident_password_resets')->updateOrInsert(
                ['email' => $email],
                ['token_hash' => hash('sha256', $token), 'created_at' => now()]
            );

            $link = url('/reset-password/' . $token) . '?email=' . urlencode($email);

            try {
                Mail::send('emails.resident-password-reset', [
                    'name'    => $resident->first_name,
                    'link'    => $link,
                    'minutes' => self::EXPIRES_MINUTES,
                ], function ($m) use ($email) {
                    $m->to($email)->subject('Reset your Tugon password');
                });
            } catch (Throwable $e) {
                // Still show the same message; the error goes to storage/logs/laravel.log
                report($e);
            }
        }

        return back()->with('sent', $email);
    }

    // The page the email links to
    public function edit(Request $request, string $token)
    {
        $email = strtolower(trim((string) $request->query('email', '')));

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
            'valid' => $this->validRecord($email, $token) !== null,
        ]);
    }

    public function update(Request $request)
    {
        // Looked up first so the new password can be checked against the person's name
        $email = strtolower(trim((string) $request->input('email', '')));
        $resident = $email !== '' ? Resident::where('email', $email)->first() : null;

        $data = $request->validate([
            'token'    => ['required', 'string'],
            'email'    => ['required', 'email'],
            'password' => StrongPassword::rules([$email, $resident?->first_name, $resident?->last_name]),
        ], StrongPassword::messages());
        if (! $resident || ! $this->validRecord($email, $data['token'])) {
            return back()->withErrors(['token' => 'This reset link is invalid or has expired. Ask for a new one.']);
        }

        // The model's "hashed" cast hashes the new password
        $resident->password = $data['password'];
        $resident->save();

        // A link works only once
        DB::table('resident_password_resets')->where('email', $email)->delete();

        return redirect('/login')
            ->with('status', 'Password changed. Log in with your new password.')
            ->with('role', 'resident');
    }

    private function validRecord(string $email, string $token): ?object
    {
        if ($email === '' || $token === '') {
            return null;
        }
        $row = DB::table('resident_password_resets')->where('email', $email)->first();
        if (! $row || ! hash_equals($row->token_hash, hash('sha256', $token))) {
            return null;
        }
        if (now()->subMinutes(self::EXPIRES_MINUTES)->greaterThan($row->created_at)) {
            return null;
        }

        return $row;
    }
}
