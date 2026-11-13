<?php

namespace App\Support\PhotoCheck;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Support\EvidenceFile;
use Throwable;

// Runs the photo check for one concern and saves the result on its row.
// Basic checks always run; the Claude check runs on top when PHOTO_CHECK_DRIVER=ai.
class PhotoCheckService
{
    public function run(string $trackingCode): string
    {
        $concern = DB::table('concerns')->where('tracking_code', $trackingCode)->first();
        if (! $concern) {
            return 'missing';
        }

        $path = EvidenceFile::path($concern->evidence_path);
        if (! $path || ! is_file($path)) {
            $this->save($trackingCode, 'error', [
                BasicPhotoChecker::flag('no_file', 'Photo file is missing on the server', 'info'),
            ], 'basic');

            return 'error';
        }

        $config = config('photo_check');
        @ini_set('memory_limit', '256M'); // GD needs room for 12 MP phone photos

        try {
            $reportedAt = $concern->created_at ? Carbon::parse($concern->created_at) : now();
            $basic = (new BasicPhotoChecker($config))->check($path, [
                'latitude'    => $concern->latitude,
                'longitude'   => $concern->longitude,
                'reported_at' => $reportedAt->toDateTimeImmutable(),
            ], fn (string $sha, ?string $dhash) => DB::table('concerns')
                ->where('tracking_code', '!=', $trackingCode)
                // Only earlier reports count, so re-checks don't flag the original
                ->when($concern->created_at, fn ($q) => $q->where('created_at', '<=', $concern->created_at))
                ->where(fn ($q) => $q->where('photo_sha256', $sha)->orWhereNotNull('photo_dhash'))
                ->get(['tracking_code', 'photo_sha256', 'photo_dhash'])
                ->map(fn ($r) => (array) $r)
                ->all());
        } catch (Throwable $e) {
            Log::warning("Photo check failed for {$trackingCode}: " . $e->getMessage());
            $this->save($trackingCode, 'error', [
                BasicPhotoChecker::flag('check_failed', 'Photo could not be checked', 'info'),
            ], 'basic');

            return 'error';
        }

        $flags = $basic['flags'];
        $source = 'basic';
        $sensitive = false;

        if (($config['driver'] ?? 'basic') === 'ai') {
            try {
                $ai = (new ClaudePhotoChecker($config['ai'] ?? []))
                    ->check($path, (string) $concern->category, (string) $concern->description);
                $flags = array_merge($flags, $ai['flags']);
                $sensitive = $ai['sensitive'];
                $source = 'basic+ai';
            } catch (Throwable $e) {
                Log::warning("AI photo check failed for {$trackingCode}, using basic checks only: " . $e->getMessage());
                $source = 'basic+ai_failed';
            }
        }

        $status = BasicPhotoChecker::statusFor($flags);
        $this->save($trackingCode, $status, $flags, $source, $basic['sha256'], $basic['dhash'], $sensitive);

        return $status;
    }

    private function save(string $code, string $status, array $flags, string $source, ?string $sha = null, ?string $dhash = null, bool $sensitive = false): void
    {
        DB::table('concerns')->where('tracking_code', $code)->update([
            'photo_check_status' => $status,
            'photo_flags'        => json_encode($flags, JSON_UNESCAPED_UNICODE),
            'photo_sha256'       => $sha,
            'photo_dhash'        => $dhash,
            'photo_check_source' => $source,
            'photo_sensitive'    => $sensitive,
            'photo_checked_at'   => now(),
            // A re-check starts a fresh review
            'photo_review'       => null,
            'photo_reviewed_by'  => null,
            'photo_reviewed_at'  => null,
        ]);
    }
}
