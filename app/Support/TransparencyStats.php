<?php

namespace App\Support;

use App\Models\Concern;
use Illuminate\Support\Facades\Schema;

/**
 * Barangay-wide concern totals for the public Transparency section on the landing page.
 * Totals only: this never returns a single report, a name or a description.
 */
class TransparencyStats
{
    // Concern statuses grouped into the 3 buckets people see everywhere
    private const BUCKET = ['pending' => 'pending', 'review' => 'progress', 'progress' => 'progress', 'blotter' => 'progress', 'resolved' => 'resolved', 'rejected' => 'rejected'];

    // An average from only a few cases says little and could point at one person,
    // so it stays hidden until there are enough resolved reports.
    public const MIN_FOR_AVERAGE = 5;

    public static function get(): array
    {
        $cols = Schema::getColumnListing((new Concern)->getTable());
        $pick = array_values(array_filter(['status', 'category', 'purok', 'created_at', 'updated_at', in_array('resolved_at', $cols, true) ? 'resolved_at' : null]));

        $rows = Concern::query()->get($pick);
        $bucket = fn ($s) => self::BUCKET[$s] ?? 'progress';

        $total = $rows->count();
        $resolvedRows = $rows->filter(fn ($c) => $bucket($c->status) === 'resolved');
        $resolved = $resolvedRows->count();

        $avgDays = $resolved
            ? round($resolvedRows->avg(fn ($c) => $c->created_at->diffInHours($c->resolved_at ?? $c->updated_at) / 24), 1)
            : 0;

        $cat = $rows->countBy(fn ($c) => $c->category ?: 'Other')->sortDesc();
        $zone = $rows->countBy(fn ($c) => $c->purok ?: 'Unspecified')->sortKeys(SORT_NATURAL);

        return [
            'total'      => $total,
            'resolved'   => $resolved,
            'rate'       => $total > 0 ? (int) round(($resolved / $total) * 100) : 0,
            'avgDays'    => $avgDays,
            'showAvg'    => $resolved >= self::MIN_FOR_AVERAGE,
            'byStatus'   => [
                $rows->filter(fn ($c) => $bucket($c->status) === 'pending')->count(),
                $rows->filter(fn ($c) => $bucket($c->status) === 'progress')->count(),
                $resolved,
            ],
            'catLabels'  => $cat->keys()->values()->all(),
            'catData'    => $cat->values()->all(),
            'zoneLabels' => $zone->keys()->values()->all(),
            'zoneData'   => $zone->values()->all(),
            'asOf'       => now()->format('M j, Y g:i A'),
        ];
    }
}
