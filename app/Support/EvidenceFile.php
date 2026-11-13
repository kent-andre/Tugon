<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

// Where a concern photo is on disk. New photos are private (storage/app/private/evidence,
// not reachable from the web). Older photos may still be on the public disk until
// "php artisan photo:make-private" moves them, so both places are checked.
class EvidenceFile
{
    public const DISK = 'local';

    public static function path(?string $relative): ?string
    {
        if (! $relative || str_contains($relative, '..')) {
            return null;
        }
        foreach ([self::DISK, 'public'] as $disk) {
            if (Storage::disk($disk)->exists($relative)) {
                return Storage::disk($disk)->path($relative);
            }
        }

        return null;
    }
}
