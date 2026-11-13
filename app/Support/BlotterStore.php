<?php

namespace App\Support;

use App\Models\Blotter;

/**
 * Blotter entries are saved in the blotters table.
 * The method names and array keys match the old sample-data version, so
 * BlotterController and the admin views keep working.
 */
class BlotterStore
{
    // Newest first.
    public static function all(): array
    {
        return Blotter::orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Blotter $b) => self::toRow($b))
            ->all();
    }

    public static function find(int $id): ?array
    {
        $blotter = Blotter::find($id);

        return $blotter ? self::toRow($blotter) : null;
    }

    public static function create(array $data): array
    {
        // refresh() loads the column defaults (status, recorded_by) after saving
        return self::toRow(Blotter::create($data)->refresh());
    }

    public static function update(int $id, array $changes): ?array
    {
        $blotter = Blotter::find($id);

        if (! $blotter) {
            return null;
        }

        $blotter->update($changes);

        return self::toRow($blotter->fresh());
    }

    // How many cases already have a CFA number this year.
    public static function cfaCount(int $year): int
    {
        return Blotter::where('cfa_no', 'like', "CFA-{$year}-%")->count();
    }

    // Dates are plain strings so date(strtotime()) in the controller shows
    // the same day and time that was saved.
    private static function toRow(Blotter $b): array
    {
        $row = $b->attributesToArray();
        $row['created_at'] = $b->created_at->toDateTimeString();
        $row['updated_at'] = $b->updated_at->toDateTimeString();

        foreach (['hearing_at', 'summons_sent_at', 'served_at'] as $col) {
            $row[$col] = $b->$col ? $b->$col->toDateTimeString() : null;
        }

        return $row;
    }
}
