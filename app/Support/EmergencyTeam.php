<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * The barangay emergency team's status, set by officials on the admin dashboard
 * and shown next to the barangay hotline on the landing page.
 * Kept in one small file (storage/app/private/emergency-team.json), so no database change is needed.
 */
class EmergencyTeam
{
    // Placeholder number. Change both lines to the real barangay hotline before the defense.
    public const HOTLINE = '0917 123 4567';
    public const HOTLINE_TEL = '+639171234567';

    // status => [label for officials, text for residents]
    public const STATUSES = [
        'available'  => ['Available', 'Available now'],
        'responding' => ['Responding', 'Out on a call right now'],
        'off'        => ['Off duty', 'Off duty. For emergencies, call 911'],
    ];

    private const FILE = 'emergency-team.json';

    public static function current(): array
    {
        $data = [];
        try {
            if (Storage::disk('local')->exists(self::FILE)) {
                $data = json_decode(Storage::disk('local')->get(self::FILE), true) ?: [];
            }
        } catch (Throwable $e) {
            // Unreadable file: fall back to Available below
        }

        $status = isset(self::STATUSES[$data['status'] ?? '']) ? $data['status'] : 'available';

        return [
            'status'     => $status,
            'label'      => self::STATUSES[$status][0],
            'public'     => self::STATUSES[$status][1],
            'updated_at' => $data['updated_at'] ?? null,
            'updated'    => ! empty($data['updated_at']) ? \Illuminate\Support\Carbon::parse($data['updated_at'])->format('M j, g:i A') : null,
            'by'         => $data['by'] ?? null,
        ];
    }

    public static function set(string $status, ?string $by): void
    {
        Storage::disk('local')->put(self::FILE, json_encode([
            'status'     => $status,
            'updated_at' => now()->toIso8601String(),
            'by'         => $by,
        ]));
    }
}
