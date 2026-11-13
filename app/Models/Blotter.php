<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blotter extends Model
{
    protected $fillable = [
        'type', 'zone', 'complainant', 'complainant_info', 'complainant_email',
        'respondent', 'respondent_info', 'respondent_email',
        'narrative', 'incident', 'place', 'witnesses', 'recorded_by', 'referred_to',
        'source_concern_code', 'status', 'hearing_at', 'hearing_place', 'mediator',
        'settlement', 'cfa_no', 'summons_sent_at', 'served_by', 'served_at', 'service_method',
    ];

    protected $casts = [
        'hearing_at'      => 'datetime',
        'summons_sent_at' => 'datetime',
        'served_at'       => 'datetime',
    ];
}
