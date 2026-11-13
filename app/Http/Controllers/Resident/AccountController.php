<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Support\Current;
use App\Support\StrongPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Settings > Change Password for a logged-in resident.
class AccountController extends Controller
{
    public function updatePassword(Request $request)
    {
        $resident = Resident::find(Current::residentId());
        abort_unless($resident, 403);

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => array_merge(
                StrongPassword::rules([$resident->first_name, $resident->last_name, $resident->email]),
                ['different:current_password']
            ),
        ], [
            'password.different' => 'The new password must be different from the current one.',
        ] + StrongPassword::messages());

        if (! Hash::check($data['current_password'], $resident->password)) {
            return back()->withErrors(['current_password' => 'Your current password is not correct.']);
        }

        // The model's "hashed" cast hashes the new password
        $resident->password = $data['password'];
        $resident->save();

        // Any reset link sent earlier stops working
        DB::table('resident_password_resets')->where('email', $resident->email)->delete();

        // New session id, so an old copied session cookie stops working
        $request->session()->regenerate();

        return back()->with('password_status', 'Password changed. Use the new one next time you log in.');
    }
}
