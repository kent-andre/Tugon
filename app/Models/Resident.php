<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'birthdate', 'gender', 'civil_status', 'occupation',
        'email', 'password', 'purok', 'address', 'contact', 'emergency_contact', 'id_type', 'valid_id_path',
        'status', 'resident_no', 'approved_at', 'reject_reason',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password'    => 'hashed',
        'birthdate'   => 'date',
        'approved_at' => 'datetime',
    ];

    public function fullName(): string
    {
        return trim(preg_replace('/\s+/', ' ', "{$this->first_name} {$this->middle_name} {$this->last_name}"));
    }
}
