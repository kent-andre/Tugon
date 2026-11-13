<?php

namespace App\Console\Commands;

use App\Support\PhotoCheck\PhotoCheckService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

// php artisan photo-check PB-2026-K7M2XQ   re-check one report
// php artisan photo-check --pending        check reports not checked yet (run once after migrating)
// php artisan photo-check --all            re-check every report (clears photo reviews)
class PhotoCheckCommand extends Command
{
    protected $signature = 'photo-check {code? : Tracking code} {--pending} {--all}';

    protected $description = 'Run the concern photo check';

    public function handle(PhotoCheckService $service): int
    {
        if ($code = $this->argument('code')) {
            $this->line("{$code}: " . $service->run($code));

            return self::SUCCESS;
        }

        if (! $this->option('pending') && ! $this->option('all')) {
            $this->error('Give a tracking code, --pending or --all.');

            return self::FAILURE;
        }

        // Oldest first so duplicates point at the earlier report
        $query = DB::table('concerns')->orderBy('created_at')->orderBy('tracking_code');
        if (! $this->option('all')) {
            $query->where('photo_check_status', 'pending');
        }
        $codes = $query->pluck('tracking_code');

        $counts = [];
        $bar = $this->output->createProgressBar($codes->count());
        foreach ($codes as $c) {
            $status = $service->run($c);
            $counts[$status] = ($counts[$status] ?? 0) + 1;
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();

        foreach ($counts as $status => $n) {
            $this->line("{$status}: {$n}");
        }

        return self::SUCCESS;
    }
}
