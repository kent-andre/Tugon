<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Temporary storage for concerns: one JSON file (storage/app/.../concerns.json).
 * Final term: swap the bodies of these methods for Eloquent/MySQL queries
 * and nothing else in the system has to change.
 */
class ConcernStore
{
    private const FILE = 'concerns.json';

    public static function all(): array
    {
        if (! Storage::exists(self::FILE)) {
            return [];
        }
        return json_decode(Storage::get(self::FILE), true) ?: [];
    }

    public static function find(string $code): ?array
    {
        foreach (self::all() as $concern) {
            if ($concern['tracking_code'] === $code) {
                return $concern;
            }
        }
        return null;
    }

    public static function create(array $data): array
    {
        $all = self::all();

        $data['id']           = count($all) + 1;
        $data['status']       = 'pending';      // pending, progress, resolved, blotter
        $data['verification'] = 'unverified';   // unverified, verified, flagged
        $data['flag_reason']     = null;
        $data['assigned_to']     = null;
        $data['internal_notes']  = null;
        $data['public_update']   = null;
        $data['created_at']      = now()->toDateTimeString();
        $data['updated_at']      = $data['created_at'];

        array_unshift($all, $data); // newest first
        self::write($all);

        return $data;
    }

    public static function update(string $code, array $changes): ?array
    {
        $all = self::all();
        $updated = null;

        foreach ($all as &$concern) {
            if ($concern['tracking_code'] === $code) {
                $concern = array_merge($concern, $changes, ['updated_at' => now()->toDateTimeString()]);
                $updated = $concern;
                break;
            }
        }
        unset($concern);

        if ($updated) {
            self::write($all);
        }
        return $updated;
    }

    public static function codeExists(string $code): bool
    {
        return self::find($code) !== null;
    }

    private static function write(array $all): void
    {
        Storage::put(self::FILE, json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
