<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Support\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

// Official Accounts page (Punong Barangay only): add officials, change their
// role, disable them, or set a temporary password.
class OfficialAccountController extends Controller
{
    public function index()
    {
        return view('admin.official-accounts', [
            'admins' => Admin::orderByRaw("role = 'captain' desc")->orderBy('name')->get(),
            'roles'  => Admin::ROLES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:120'],
            'position' => ['required', 'string', 'max:120'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('admins', 'email')],
            'role'     => ['required', Rule::in(array_keys(Admin::ROLES))],
            'password' => ['required', 'string', 'min:8'],
        ]);
        $data['email'] = strtolower(trim($data['email']));

        $admin = Admin::create($data);
        AuditLog::record("Added official account for {$admin->name} ({$admin->position})", 'Accounts');

        return back()->with('ok', "Account created for {$admin->name}. Give them the temporary password and ask them to change it.");
    }

    public function update(Request $request, Admin $admin)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:120'],
            'position'  => ['required', 'string', 'max:120'],
            'role'      => ['required', Rule::in(array_keys(Admin::ROLES))],
            'is_active' => ['required', 'boolean'],
            'password'  => ['nullable', 'string', 'min:8'],
        ]);

        // You cannot lock yourself out.
        if ($admin->id === session('admin.id') && ($data['role'] !== 'captain' || ! $data['is_active'])) {
            return back()->withErrors(['account' => 'You cannot change your own role or disable your own account.']);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $admin->update($data);
        AuditLog::record("Updated official account for {$admin->name}" . (isset($data['password']) ? ', password reset' : ''), 'Accounts');

        return back()->with('ok', "Saved changes for {$admin->name}.");
    }

    // Any official can change their own password.
    public function passwordForm()
    {
        return view('admin.password');
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ]);

        $admin = Admin::findOrFail(session('admin.id'));

        if (! Hash::check($data['current_password'], $admin->password)) {
            return back()->withErrors(['current_password' => 'Your current password is incorrect.']);
        }

        $admin->update(['password' => $data['password']]);
        AuditLog::record('Changed own password', 'Accounts');

        return back()->with('ok', 'Your password was changed.');
    }
}
