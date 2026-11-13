<?php

namespace App\Support;

use App\Models\Admin;
use App\Models\Resident;

/**
 * One place that knows how "who is logged in" is stored.
 * If your Step 5 code keeps these somewhere else, change ONLY this file.
 */
class Current
{
    /** The admin set by EnsureAdmin (it shares $me with the views). */
    public static function admin(): ?Admin
    {
        $me = view()->shared('me');
        if ($me instanceof Admin) {
            return $me;
        }
        $id = session('admin.id') ?? session('admin_id');
        return $id ? Admin::find($id) : null;
    }

    public static function adminId(): ?int
    {
        return self::admin()?->id;
    }

    public static function adminName(): string
    {
        $a = self::admin();
        if (! $a) {
            return 'Barangay Alejal Office';
        }
        $name = $a->name ?? trim(($a->first_name ?? '') . ' ' . ($a->last_name ?? ''));
        return $name !== '' ? $name : ($a->username ?? 'Admin #' . $a->id);
    }

    /** Resident login stores the resident in session('resident') (see home.blade.php). */
    public static function residentId(): ?int
    {
        $id = session('resident.id') ?? session('resident_id');
        return $id ? (int) $id : null;
    }

    public static function resident(): ?Resident
    {
        $id = self::residentId();
        return $id ? Resident::find($id) : null;
    }
}
