<?php

namespace App\Http\Controllers;

use App\Support\ConcernStore;

// Public lookup used by /check-status. Only returns what a resident may see:
// never the contact, name, internal notes, assignee or verification result.
class ConcernStatusController extends Controller
{
    private const LABELS = [
        'pending'  => 'Pending',
        'progress' => 'In Progress',
        'resolved' => 'Resolved',
        'blotter'  => 'In Progress',
    ];

    private const DEFAULT_NOTES = [
        'pending'  => 'Waiting for a barangay official to review.',
        'progress' => 'A barangay official is handling your report.',
        'resolved' => 'This report has been resolved.',
        'blotter'  => 'Your report was recorded in the barangay blotter. Please visit the barangay hall if you have not yet signed your statement.',
    ];

    public function show(string $code)
    {
        $concern = ConcernStore::find(strtoupper(trim($code)));

        if (! $concern) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $status = $concern['status'] ?? 'pending';

        return response()->json([
            'tracking_code' => $concern['tracking_code'],
            'status'        => self::LABELS[$status] ?? 'Pending',
            'category'      => $concern['category'],
            'submitted'     => date('M j, Y', strtotime($concern['created_at'])),
            'updated'       => date('M j, Y', strtotime($concern['updated_at'])),
            'note'          => ($concern['public_update'] ?? '') ?: (self::DEFAULT_NOTES[$status] ?? ''),
        ]);
    }
}