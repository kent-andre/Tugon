<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// The four sample blotters from the old BlotterStore. The ids are fixed so
// concern PB-2026-0003 still points to BR-00004. Safe to run again; it also
// puts BR-00001 back to Filed so the summons can be tested again.
class BlotterSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'complainant_info'    => '',
            'complainant_email'   => null,
            'respondent'          => '',
            'respondent_info'     => '',
            'respondent_email'    => null,
            'witnesses'           => '',
            'recorded_by'         => 'Barangay Secretary',
            'referred_to'         => '',
            'source_concern_code' => null,
            'hearing_at'          => null,
            'hearing_place'       => null,
            'summons_sent_at'     => null,
            'served_by'           => null,
            'served_at'           => null,
            'service_method'      => null,
            'mediator'            => null,
            'settlement'          => null,
            'cfa_no'              => null,
        ];

        $rows = [
            [
                'id' => 1, 'type' => 'Neighbor Dispute', 'zone' => 'Purok 2',
                'complainant' => 'Pedro Ramos', 'complainant_info' => 'Purok 2 resident',
                'complainant_email' => 'pedro.ramos@example.com',
                'respondent' => 'Mario Cruz', 'respondent_info' => 'Purok 2 resident',
                'respondent_email' => 'mario.cruz@example.com',
                'narrative' => 'Disagreement about where the boundary fence between the two houses should stand.',
                'incident' => 'Oct 8, 2026', 'place' => 'Purok 2, boundary fence',
                'referred_to' => 'Lupon Tagapamayapa', 'status' => 'filed',
                'created_at' => '2026-10-09 08:45:00',
            ],
            [
                'id' => 2, 'type' => 'Noise Complaint', 'zone' => 'Purok 5',
                'complainant' => 'Luis Garcia', 'complainant_info' => 'Purok 5 resident',
                'respondent' => 'Dante Flores', 'respondent_info' => 'Purok 5 resident',
                'narrative' => 'Loud videoke past midnight on several nights despite earlier warnings.',
                'incident' => 'Oct 4, 2026', 'place' => 'Purok 5, Rizal Street',
                'witnesses' => 'Two neighbors', 'referred_to' => 'Lupon Tagapamayapa',
                'status' => 'hearing', 'mediator' => 'Kgd. Reyes',
                'hearing_at' => '2026-10-16 15:00:00', 'hearing_place' => 'Barangay Hall, Barangay Alejal',
                'created_at' => '2026-10-05 11:15:00',
            ],
            [
                'id' => 3, 'type' => 'Neighbor Dispute', 'zone' => 'Purok 3',
                'complainant' => 'Ana Lopez', 'complainant_info' => 'Purok 3 resident',
                'respondent' => 'Rosa Villar', 'respondent_info' => 'Purok 3 resident',
                'narrative' => "Dispute over a shared drainage pipe that overflowed into the complainant's yard.",
                'incident' => 'Sep 28, 2026', 'place' => 'Purok 3, shared fence line',
                'referred_to' => 'Lupon Tagapamayapa', 'status' => 'settled',
                'mediator' => 'Kgd. Mendoza',
                'settlement' => 'Both parties agreed to share the cost of repairing the pipe.',
                'hearing_at' => '2026-10-02 14:00:00',
                'created_at' => '2026-09-29 10:30:00',
            ],
            [
                'id' => 4, 'type' => 'Crime/Safety', 'zone' => 'Purok 1',
                'complainant' => 'Juan Dela Cruz', 'complainant_info' => 'Purok 1 resident',
                'respondent' => 'Unknown persons',
                'narrative' => 'Residents reported unknown persons loitering near the elementary school at night.',
                'incident' => 'Oct 5, 2026', 'place' => 'Near the elementary school',
                'source_concern_code' => 'PB-2026-0003', 'status' => 'recorded',
                'created_at' => '2026-10-06 09:00:00',
            ],
        ];

        foreach ($rows as $row) {
            $row['updated_at'] = $row['created_at'];

            DB::table('blotters')->updateOrInsert(
                ['id' => $row['id']],
                array_merge($defaults, $row)
            );
        }
    }
}
