<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Official extends Model
{
    protected $fillable = ['group', 'name', 'position', 'committee', 'contact', 'term', 'photo_path', 'sort_order'];

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }

    /** Lupon members only - use this for the blotter "Mediator" dropdown. */
    public function scopeLupon(Builder $q): Builder
    {
        return $q->where('group', 'lupon');
    }

    public function initials(): string
    {
        $clean = preg_replace('/\b(Hon\.|Mr\.|Mrs\.|Ms\.|Ma\.)\s*/', '', $this->name);
        return collect(preg_split('/\s+/', trim($clean)))->filter()->take(2)
            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    }

    /** Bundled photos live in public/images, uploaded ones on the public disk. */
    public function photoUrl(): ?string
    {
        if (! $this->photo_path) {
            return null;
        }
        return str_starts_with($this->photo_path, 'images/')
            ? asset($this->photo_path)
            : Storage::disk('public')->url($this->photo_path);
    }
}
