<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// The six sample concerns from the old ConcernStore, so the admin pages
// have something to show. Safe to run again, it will not add duplicates.
class ConcernSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'reporter_name'  => null,
            'contact'        => '',
            'is_anonymous'   => false,
            'latitude'       => null,
            'longitude'      => null,
            'evidence_path'  => null,
            'status'         => 'pending',
            'verification'   => 'unverified',
            'flag_reason'    => null,
            'assigned_to'    => null,
            'internal_notes' => null,
            'public_update'  => null,
            'blotter_ref'    => null,
        ];

        // Oldest first, so the ids come out as 1 to 6
        $rows = [
            [
                'tracking_code' => 'PB-2026-0001', 'category' => 'Streetlights',
                'description' => 'The streetlight in front of the basketball court has been out for two weeks.',
                'purok' => 'Purok 2', 'reporter_name' => 'Maria Santos', 'contact' => '0917-000-0101',
                'latitude' => 7.4215, 'longitude' => 125.7062,
                'created_at' => '2026-10-01 18:45:00', 'updated_at' => '2026-10-01 18:45:00',
            ],
            [
                'tracking_code' => 'PB-2026-0002', 'category' => 'Environment',
                'description' => 'Garbage along the creek is not collected and is clogging the drain.',
                'purok' => 'Purok 4', 'is_anonymous' => true, 'contact' => '0917-000-0102',
                'latitude' => 7.4182, 'longitude' => 125.7120,
                'status' => 'progress', 'verification' => 'verified', 'assigned_to' => 'Kgd. Reyes',
                'public_update' => 'Garbage pickup is scheduled for this week.',
                'created_at' => '2026-10-03 14:25:00', 'updated_at' => '2026-10-04 10:00:00',
            ],
            [
                'tracking_code' => 'PB-2026-0003', 'category' => 'Crime/Safety',
                'description' => 'Suspicious people loitering near the elementary school at night.',
                'purok' => 'Purok 1', 'reporter_name' => 'Juan Dela Cruz', 'contact' => '0917-000-0103',
                'latitude' => 7.4236, 'longitude' => 125.7084,
                'status' => 'blotter', 'verification' => 'verified', 'blotter_ref' => 'BR-00004',
                'created_at' => '2026-10-05 19:10:00', 'updated_at' => '2026-10-06 09:00:00',
            ],
            [
                'tracking_code' => 'PB-2026-0004', 'category' => 'Water Supply',
                'description' => 'No water supply in our street since Monday morning.',
                'purok' => 'Purok 3', 'reporter_name' => 'Ana Lopez', 'contact' => '0917-000-0104',
                'latitude' => 7.4199, 'longitude' => 125.7035,
                'status' => 'resolved', 'verification' => 'verified', 'assigned_to' => 'Kgd. Mendoza',
                'public_update' => 'The pipe was repaired and water supply is back.',
                'created_at' => '2026-10-07 07:20:00', 'updated_at' => '2026-10-09 15:30:00',
            ],
            [
                'tracking_code' => 'PB-2026-0005', 'category' => 'Noise',
                'description' => 'Videoke until 2 AM almost every night on the same street.',
                'purok' => 'Purok 5', 'is_anonymous' => true, 'contact' => '0917-000-0105',
                'latitude' => 7.4228, 'longitude' => 125.7101,
                'verification' => 'flagged', 'flag_reason' => 'Possible duplicate of an earlier report.',
                'created_at' => '2026-10-10 21:40:00', 'updated_at' => '2026-10-10 21:40:00',
            ],
            [
                'tracking_code' => 'PB-2026-0006', 'category' => 'Infrastructure',
                'description' => 'Large pothole on the main road near the barangay hall.',
                'purok' => 'Purok 2', 'reporter_name' => 'Pedro Ramos', 'contact' => '0917-000-0106',
                'status' => 'progress', 'verification' => 'verified', 'assigned_to' => 'Kgd. Reyes',
                'public_update' => 'The road crew has been scheduled for repair.',
                'created_at' => '2026-10-11 10:05:00', 'updated_at' => '2026-10-12 08:00:00',
            ],
        ];

        foreach ($rows as $row) {
            DB::table('concerns')->updateOrInsert(
                ['tracking_code' => $row['tracking_code']],
                array_merge($defaults, $row)
            );
        }
    }
}
