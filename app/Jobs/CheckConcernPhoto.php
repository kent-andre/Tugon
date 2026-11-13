<?php

namespace App\Jobs;

use App\Support\PhotoCheck\PhotoCheckService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// Dispatched with dispatchAfterResponse() from ConcernController, so it runs
// right after the resident gets the tracking code. If a queue worker is set up
// later, change that call to dispatch() and it moves to the queue.
class CheckConcernPhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(public string $trackingCode)
    {
    }

    public function handle(PhotoCheckService $service): void
    {
        $service->run($this->trackingCode);
    }
}
