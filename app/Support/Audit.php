<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Step 6 controllers call Audit::log(). This just passes the entry to the
 * project's own AuditLog::record(), so every audit row has the same format
 * (official's name, category, IP) as the rest of the Audit Log page.
 * An audit failure is reported but never blocks the official's action.
 */
class Audit
{
    public static function log(string $action, string $target = '', array $details = []): void
    {
        $text = $target !== '' ? "{$action}: {$target}" : $action;
        if (! empty($details['note'])) {
            $text .= ' (note: ' . Str::limit((string) $details['note'], 80) . ')';
        }

        $a = strtolower($action);
        $category = match (true) {
            str_contains($a, 'photo')        => 'Concerns',
            str_contains($a, 'document')     => 'Documents',
            str_contains($a, 'announcement'),
            str_contains($a, 'popup')        => 'Announcements',
            str_contains($a, 'official')     => 'Officials',
            str_contains($a, 'calendar')     => 'Calendar',
            default                          => 'Accounts',
        };

        try {
            AuditLog::record($text, $category);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
