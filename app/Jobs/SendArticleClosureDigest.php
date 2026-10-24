<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\ArticleClosure\SendBatch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendArticleClosureDigest implements ShouldQueue
{
    use Queueable;

    public int $timeout = 60;

    public int $tries = 1;

    public bool $failOnTimeout = true;

    public function __construct(public int $batchId) {}

    public function handle(SendBatch $send): void
    {
        $send($this->batchId);
    }

    public function failed(?Throwable $exception): void
    {
        Log::warning('Article closure digest job failed; recovery will reconcile its delivery state', ['batch_id' => $this->batchId]);
    }
}
