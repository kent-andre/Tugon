<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRequestLog extends Model
{
    public const UPDATED_AT = null;   // history rows are never edited

    protected $fillable = ['document_request_id', 'status', 'admin_id'];

    protected $casts = ['created_at' => 'datetime'];
}
