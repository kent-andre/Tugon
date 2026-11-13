<?php

namespace App\Console\Commands;

use App\Support\EvidenceFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// php artisan photo:make-private
// Moves concern photos from the public folder (storage/app/public/evidence, reachable
// by link) to private storage. Safe to run more than once.
class PhotoMakePrivateCommand extends Command
{
    protected $signature = 'photo:make-private';

    protected $description = 'Move concern photos from public to private storage';

    public function handle(): int
    {
        $public = Storage::disk('public');
        $private = Storage::disk(EvidenceFile::DISK);
        $moved = $already = $missing = 0;

        foreach (DB::table('concerns')->whereNotNull('evidence_path')->where('evidence_path', '!=', '')->pluck('evidence_path') as $rel) {
            if ($private->exists($rel)) {
                if ($public->exists($rel)) {
                    $public->delete($rel); // leftover public copy
                }
                $already++;
            } elseif ($public->exists($rel)) {
                $private->put($rel, $public->get($rel));
                if ($private->exists($rel)) {
                    $public->delete($rel);
                    $moved++;
                }
            } else {
                $missing++;
            }
        }

        $this->info("Moved to private: {$moved}");
        $this->line("Already private: {$already}");
        $this->line("Photo file not found: {$missing}");

        return self::SUCCESS;
    }
}
