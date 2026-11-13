<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Concern extends Model
{
    protected $fillable = [
        'tracking_code', 'category', 'description', 'purok', 'reporter_name', 'contact',
        'is_anonymous', 'resident_id', 'latitude', 'longitude', 'location_accuracy', 'location_adjusted', 'evidence_path',
        'status', 'verification', 'flag_reason', 'assigned_to',
        'internal_notes', 'public_update', 'blotter_ref',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'latitude'     => 'float',
        'longitude'    => 'float',
        'location_accuracy' => 'integer',
        'location_adjusted' => 'boolean',
        // Photo check (Step 8). These columns are written by PhotoCheckService and
        // Admin\PhotoReviewController with DB::table, so they are not in $fillable.
        'photo_flags'      => 'array',
        'photo_sensitive'  => 'boolean',
        'photo_checked_at' => 'datetime',
        'photo_reviewed_at' => 'datetime',
    ];
}