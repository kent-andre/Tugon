<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Audit;
use App\Support\EmergencyTeam;
use Illuminate\Http\Request;

// Officials set the emergency team status from the admin dashboard.
// Residents see it on the landing page next to the barangay hotline.
class EmergencyTeamController extends Controller
{
    public function update(Request $request)
    {
        // Only signed-in officials can change it
        abort_unless(session()->has('admin.name'), 403);

        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(EmergencyTeam::STATUSES))],
        ]);

        EmergencyTeam::set($data['status'], session('admin.name'));
        Audit::log('Set emergency team status', EmergencyTeam::STATUSES[$data['status']][0]);

        return back()->with('team_saved', 'Emergency team status set to ' . EmergencyTeam::STATUSES[$data['status']][0] . '.');
    }

    // Small public answer the landing page checks every minute, so residents see changes without reloading
    public function status()
    {
        $t = EmergencyTeam::current();

        return response()->json([
            'status'  => $t['status'],
            'text'    => $t['public'],
            'updated' => $t['updated'],
        ])->header('Cache-Control', 'no-store');
    }
}
