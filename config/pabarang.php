<?php

/*
 | Shared lists for Tugon. Admin and resident pages read these so the
 | labels, icons and allowed values can never drift apart again.
 */
return [

    // Barangay details used on summons, emails and printed forms. Set them in .env.
    'barangay_name'    => env('BARANGAY_NAME', 'Barangay Alejal'),
    'barangay_address' => env('BARANGAY_ADDRESS', 'Carmen, Davao del Norte'),
    'captain_name'     => env('BARANGAY_CAPTAIN', 'Hon. Liza Bugais Adran'),
    'hearing_place'    => env('HEARING_PLACE', 'Barangay Hall, Barangay Alejal'),

    // Document request types (key = value stored in document_requests.type)
    'doc_types' => [
        'clearance'   => ['name' => 'Barangay Clearance',       'icon' => 'ti-file-certificate',
                          'purposes' => ['Employment', 'School Requirement', 'Travel / Passport Application', 'Business Permit', 'Other']],
        'residency'   => ['name' => 'Certificate of Residency', 'icon' => 'ti-home-check',
                          'purposes' => ['School Enrollment', 'Work Requirement', 'Travel', 'Other']],
        'indigency'   => ['name' => 'Certificate of Indigency', 'icon' => 'ti-heart-handshake',
                          'purposes' => ['Medical Assistance', 'Scholarship Application', 'Burial Assistance', 'Other']],
        'business'    => ['name' => 'Business Clearance',       'icon' => 'ti-briefcase'],
        'vaccination' => ['name' => 'Vaccination Scheduling',   'icon' => 'ti-vaccine',
                          'vaccines' => ['COVID-19', 'Flu (Influenza)', 'Anti-Rabies', 'Pneumonia', 'Routine Child Immunization', 'Other']],
        'id'          => ['name' => 'Senior Citizen / PWD ID',  'icon' => 'ti-id-badge-2',
                          'disabilities' => ['Physical/Mobility', 'Visual', 'Hearing', 'Intellectual', 'Psychosocial', 'Other']],
    ],

    // Announcement categories (key => tag label shown to residents)
    'announcement_categories' => [
        'health'     => 'HEALTH',
        'advisory'   => 'ADVISORY',
        'utility'    => 'UTILITY NOTICE',
        'event'      => 'EVENTS',
        'assistance' => 'ASSISTANCE PROGRAMS',
    ],

    // Calendar categories: label and color. Blotter hearings use 'hearing'
    // (admin calendar only, never shown to residents).
    'event_categories' => [
        'assembly'  => ['label' => 'Assembly / Session',      'color' => '#4F5FE0'],
        'health'    => ['label' => 'Health',                  'color' => '#1E9E5A'],
        'community' => ['label' => 'Community',               'color' => '#E08A1E'],
        'youth'     => ['label' => 'Youth / SK',              'color' => '#8E44AD'],
        'holiday'   => ['label' => 'Holiday / Office closed', 'color' => '#D64545'],
    ],
    'hearing_color' => '#6B7280',

    // FR-33 / DB-09: exactly four official groups, in display order.
    'official_groups' => [
        'council' => 'Barangay Council',
        'sk'      => 'Sangguniang Kabataan (SK)',
        'tanod'   => 'Barangay Tanod',
        'lupon'   => 'Lupong Tagapamayapa',
    ],
];
