<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    // Filter dropdown. The keys are also the badge color classes in the view.
    private const CATEGORIES = [
        'concerns'      => 'Concerns',
        'documents'     => 'Documents',
        'blotters'      => 'Blotters',
        'announcements' => 'Announcements',
        'officials'     => 'Officials',
        'accounts'      => 'Accounts',
        'system'        => 'System',
    ];

    // Only the latest rows are loaded so the page stays fast.
    private const LIMIT = 500;

    public function index()
    {
        $logs = DB::table('audit_logs')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(self::LIMIT)
            ->get()
            ->map(function ($r) {
                $at = Carbon::parse($r->created_at);

                return [
                    'at'       => $at->format('M j, Y, g:i A'),
                    'ts'       => $at->getTimestampMs(),
                    'action'   => $r->action,
                    'actor'    => $r->actor ?? $this->noActorLabel($r->action),
                    'category' => $this->key($r->category),
                    'ip'       => $r->ip_address ?? '',
                ];
            })
            ->all();

        return view('admin.audit-log', [
            'logs'           => $logs,
            'totalCount'     => DB::table('audit_logs')->count(),
            'todayCount'     => DB::table('audit_logs')->whereDate('created_at', today())->count(),
            'weekCount'      => DB::table('audit_logs')->where('created_at', '>=', now()->subDays(7))->count(),
            'failedCount'    => DB::table('audit_logs')->where('action', 'like', 'Failed login%')->count(),
            'categoryLabels' => self::CATEGORIES,
            'limit'          => self::LIMIT,
        ]);
    }

    // Rows with no official's name: say why, instead of leaving it blank.
    private function noActorLabel(string $action): string
    {
        if (str_starts_with($action, 'Failed login')) {
            return 'Not logged in';
        }
        if (str_starts_with($action, 'New resident registration')) {
            return 'Resident (name not stored)';
        }

        return 'Not recorded';
    }

    // AuditLog::record() is called with names like "Blotter"; the view uses "blotters".
    private function key(string $category): string
    {
        $k = strtolower(trim($category));

        if (isset(self::CATEGORIES[$k])) {
            return $k;
        }

        return isset(self::CATEGORIES[$k . 's']) ? $k . 's' : 'system';
    }
}
