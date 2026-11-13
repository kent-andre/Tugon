<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Resident;
use Illuminate\Database\Seeder;

// Official accounts and sample residents for testing. Safe to run again.
// The Punong Barangay account uses ADMIN_EMAIL and ADMIN_PASSWORD from .env.
class AccountSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => strtolower(config('pabarang.admin_email'))],
            [
                'name'      => config('pabarang.captain_name'),
                'position'  => 'Punong Barangay',
                'password'  => config('pabarang.admin_password'),
                'role'      => 'captain',
                'is_active' => true,
            ]
        );

        // Test accounts for the other two roles. Change or delete before the real use.
        Admin::updateOrCreate(['email' => 'secretary@pabarang.test'], [
            'name' => 'Ms. Maria L. Santos', 'position' => 'Barangay Secretary',
            'password' => 'Secretary123', 'role' => 'secretary', 'is_active' => true,
        ]);
        Admin::updateOrCreate(['email' => 'staff@pabarang.test'], [
            'name' => 'Mr. Federico L. Bautista', 'position' => 'Chief Barangay Tanod',
            'password' => 'Staff12345', 'role' => 'staff', 'is_active' => true,
        ]);

        // Approved sample resident (replaces the old config/residents.php login).
        Resident::updateOrCreate(['email' => 'kentandreaguilar@gmail.com'], [
            'first_name' => 'Kent Andre', 'last_name' => 'Aguilar', 'birthdate' => '2007-03-15',
            'gender' => 'Male', 'civil_status' => 'Single', 'purok' => 'Purok 1',
            'address' => 'Purok 1, Barangay Alejal', 'contact' => '09171234567',
            'id_type' => 'Student ID', 'password' => 'Resident123',
            'status' => 'approved', 'resident_no' => 'ALJ-2026-0001', 'approved_at' => now(),
        ]);

        // Pending sample, so the approve and reject buttons can be tested.
        Resident::updateOrCreate(['email' => 'juan.delacruz@example.com'], [
            'first_name' => 'Juan', 'middle_name' => 'Santos', 'last_name' => 'Dela Cruz',
            'birthdate' => '1990-06-02', 'gender' => 'Male', 'civil_status' => 'Married',
            'occupation' => 'Farmer', 'purok' => 'Purok 3', 'address' => 'Purok 3, Barangay Alejal',
            'contact' => '09181234567', 'emergency_contact' => 'Rosa Dela Cruz - 09191234567',
            'id_type' => 'PhilSys National ID', 'password' => 'Resident123', 'status' => 'pending',
        ]);
    }
}
