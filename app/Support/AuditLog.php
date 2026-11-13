<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Writes one row to the audit_logs table: time, action, category, IP address,
 * and the official who did it (when an official is logged in).
 * Resident names are never stored here.
 */
class AuditLog
{
    public static function record(string $action, string $category): void
    {
        $admin = session('admin');

        DB::table('audit_logs')->insert([
            'action'     => $action,
            'category'   => $category,
            'actor'      => $admin ? "{$admin['name']} ({$admin['position']})" : null,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
