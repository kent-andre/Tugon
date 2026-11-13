<?php

// Photo check for concern evidence (Step 8). Flags photos for admin review;
// it never rejects or hides a report.
return [

    // basic = file checks only (no internet). ai = basic checks + Claude API.
    'driver' => env('PHOTO_CHECK_DRIVER', 'basic'),

    // Barangay center, used for photo GPS when the report has no map pin.
    'center_lat' => (float) env('PHOTO_CHECK_CENTER_LAT', 7.421),
    'center_lng' => (float) env('PHOTO_CHECK_CENTER_LNG', 125.706),

    // Photo GPS farther than this from the map pin is flagged.
    'max_km_from_pin' => (float) env('PHOTO_CHECK_MAX_KM_FROM_PIN', 1),

    // Photo GPS farther than this from the barangay center is flagged (no pin given).
    'max_km_from_center' => (float) env('PHOTO_CHECK_MAX_KM_FROM_CENTER', 3),

    // EXIF date older than this many days before the report is flagged.
    'max_photo_age_days' => (int) env('PHOTO_CHECK_MAX_AGE_DAYS', 30),

    // Smaller than this (shorter side in px, or file size in bytes) is flagged.
    'min_dimension' => (int) env('PHOTO_CHECK_MIN_DIMENSION', 300),
    'min_bytes'     => (int) env('PHOTO_CHECK_MIN_BYTES', 15 * 1024),

    // Perceptual hashes this close (0-64 bits different) count as the same photo.
    'near_duplicate_distance' => (int) env('PHOTO_CHECK_NEAR_DUPLICATE_DISTANCE', 8),

    'ai' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'model'   => env('PHOTO_CHECK_AI_MODEL', 'claude-haiku-5-5'),
        'timeout' => (int) env('PHOTO_CHECK_AI_TIMEOUT', 15),
        // Long edge in px of the copy sent to the API (smaller = cheaper).
        'max_edge' => 1568,
    ],
];
