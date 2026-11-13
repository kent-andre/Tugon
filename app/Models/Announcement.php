<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['category', 'title', 'body', 'pinned', 'posted_by', 'admin_id'];

    protected $casts = ['pinned' => 'boolean'];

    /** Pinned first, then newest. */
    public function scopeForDisplay(Builder $q): Builder
    {
        return $q->orderByDesc('pinned')->latest()->orderByDesc('id');
    }

    public function label(): string
    {
        return config("pabarang.announcement_categories.{$this->category}", strtoupper($this->category));
    }
}
