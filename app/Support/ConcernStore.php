<?php

namespace App\Support;

use App\Models\Concern;

/**
 * Concerns are saved in the concerns table.
 * The method names and the array keys are the same as the old file version,
 * so the controllers and views do not need to change.
 */
class ConcernStore
{
    // Newest first.
    public static function all(): array
    {
        return Concern::orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Concern $c) => self::toRow($c))
            ->all();
    }

    public static function find(string $code): ?array
    {
        $concern = Concern::where('tracking_code', $code)->first();

        return $concern ? self::toRow($concern) : null;
    }

    public static function create(array $data): array
    {
        // refresh() loads the column defaults (status, verification) after saving
        return self::toRow(Concern::create($data)->refresh());
    }

    public static function update(string $code, array $changes): ?array
    {
        $concern = Concern::where('tracking_code', $code)->first();

        if (! $concern) {
            return null;
        }

        $concern->update($changes);

        return self::toRow($concern->fresh());
    }

    public static function codeExists(string $code): bool
    {
        return Concern::where('tracking_code', $code)->exists();
    }

    // Dates are written out as plain strings so date(strtotime()) in the
    // controllers shows the same day and time that was saved.
    private static function toRow(Concern $c): array
    {
        $row = $c->attributesToArray();
        $row['created_at'] = $c->created_at->toDateTimeString();
        $row['updated_at'] = $c->updated_at->toDateTimeString();

        return $row;
    }
}