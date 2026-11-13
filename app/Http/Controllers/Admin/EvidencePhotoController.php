<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\EvidenceFile;
use Illuminate\Support\Facades\DB;

// Sends a concern's photo to a logged-in official (route is behind EnsureAdmin).
// Residents and guests never get a photo link, so a leaked link shows nothing.
class EvidencePhotoController extends Controller
{
    public function show(string $code)
    {
        $relative = DB::table('concerns')->where('tracking_code', $code)->value('evidence_path');
        $path = EvidenceFile::path($relative);
        abort_unless($path && is_file($path), 404);

        return response()->file($path, [
            'Cache-Control'          => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
