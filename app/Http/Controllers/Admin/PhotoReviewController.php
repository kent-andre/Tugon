<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminConcernController;
use App\Http\Controllers\Controller;
use App\Support\Audit;
use App\Support\ConcernStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// "Photo is OK" / "Keep flag" on a flagged concern photo (Step 8). The report
// itself is never changed or hidden; this only records an official's decision.
class PhotoReviewController extends Controller
{
    public function update(Request $request, string $code)
    {
        $data = $request->validate([
            'decision' => ['required', 'in:ok,kept'],
        ]);

        $updated = DB::table('concerns')->where('tracking_code', $code)->update([
            'photo_review'      => $data['decision'],
            'photo_reviewed_by' => (string) (session('admin.name') ?: 'Official'),
            'photo_reviewed_at' => now(),
        ]);
        abort_if($updated === 0 && ! ConcernStore::codeExists($code), 404);

        // Audit::log() files actions with "photo" under Concerns and records the official
        Audit::log($data['decision'] === 'ok' ? 'Marked concern photo OK' : 'Kept concern photo flag', $code);

        $message = $data['decision'] === 'ok' ? 'Photo marked OK.' : 'Photo flag kept.';

        if (! $request->expectsJson()) {
            return back()->with('success', $message);
        }

        return response()->json([
            'message' => $message,
            'photo'   => AdminConcernController::photoFor(ConcernStore::find($code)),
        ]);
    }
}
