<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ResidentPopup extends Model
{
    protected $fillable = ['enabled', 'category', 'title', 'body', 'image_path', 'updated_by'];

    protected $casts = ['enabled' => 'boolean'];

    /** There is only ever one popup (id 1). Created on first use if the seeder was not run. */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'enabled'  => false,
            'category' => 'event',
            'title'    => 'Welcome to Tugon',
            'body'     => 'Announcements from Barangay Alejal will appear here.',
        ]);
    }

    public function imageUrl(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }

    /** Changes on every save, so residents see an edited popup again. */
    public function version(): string
    {
        return (string) optional($this->updated_at)->timestamp;
    }

    public function toPreviewArray(): array
    {
        return [
            'enabled'   => $this->enabled,
            'category'  => $this->category,
            'title'     => $this->title,
            'body'      => $this->body,
            'image'     => $this->imageUrl() ?? '',
            'updatedAt' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
