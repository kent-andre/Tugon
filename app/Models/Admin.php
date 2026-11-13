<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// A barangay official who can log in to the admin side.
class Admin extends Model
{
    public const ROLES = [
        'captain'   => 'Punong Barangay (full access)',
        'secretary' => 'Secretary (all except official accounts)',
        'staff'     => 'Staff (concerns, view-only blotters)',
    ];

    protected $fillable = ['name', 'position', 'email', 'password', 'role', 'is_active', 'last_login_at'];

    protected $hidden = ['password'];

    protected $casts = [
        'password'      => 'hashed',
        'is_active'     => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // What is kept in the session and shown in the sidebar and top bar.
    public function sessionData(): array
    {
        $plain = preg_replace('/^(Hon\.|Kgd\.|Mr\.|Ms\.|Mrs\.)\s*/i', '', $this->name);

        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'position' => $this->position,
            'role'     => $this->role,
            'initial'  => mb_strtoupper(mb_substr($plain, 0, 1)),
        ];
    }
}
