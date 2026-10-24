<?php

declare(strict_types=1);

namespace App\Actions\ArticleClosure;

use App\Enums\ArticleClosureBatchStatus;
use App\Enums\ArticleClosureRunStatus;
use App\Jobs\SendArticleClosureDigest;
use App\Models\ArticleClosureBatch;
use App\Models\ArticleClosureRun;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class Recover
{
    public function __construct(private Runs $runs) {}

    public function __invoke(): void
    {
        $runCutoff = now()->subSeconds(config('article-closure.run_timeout'));
        $limit = config('article-closure.recovery_limit');
        $runIds = ArticleClosureRun::query()->where('status', ArticleClosureRunStatus::Running)
            ->where('heartbeat_at', '<=', $runCutoff)->orderBy('id')->limit($limit)->pluck('id');
        foreach ($runIds as $runId) {
            DB::transaction(function () use ($runId, $runCutoff): void {
                $run = ArticleClosureRun::query()->lockForUpdate()->whereKey($runId)->first();
                if ($run && $run->status === ArticleClosureRunStatus::Running && $run->heartbeat_at->lessThanOrEqualTo($runCutoff)) {
                    $this->runs->finalize($run->id, interrupted: true);
                }
            });
        }

        ArticleClosureBatch::query()->where('status', ArticleClosureBatchStatus::Sending)
            ->where('sending_at', '<=', now()->subSeconds(config('article-closure.sending_timeout')))
            ->update(['status' => ArticleClosureBatchStatus::Unknown, 'last_error' => 'Sending lease expired; delivery outcome must be confirmed before manual retry.']);

        $batchIds = ArticleClosureBatch::query()->whereIn('status', [ArticleClosureBatchStatus::Pending, ArticleClosureBatchStatus::Failed])
            ->where('attempts', '<', config('article-closure.max_attempts'))
            ->where(fn ($query) => $query->whereNull('next_attempt_at')->orWhere('next_attempt_at', '<=', now()))
            ->where(fn ($query) => $query->whereNull('dispatch_lease_until')->orWhere('dispatch_lease_until', '<=', now()))
            ->whereHas('run', fn ($query) => $query->whereNotNull('finalized_at'))
            ->orderBy('id')->limit($limit)->pluck('id');
        foreach ($batchIds as $batchId) {
            $claimed = DB::transaction(function () use ($batchId): bool {
                $batch = ArticleClosureBatch::query()->lockForUpdate()->whereKey($batchId)->first();
                if (! $batch || ! in_array($batch->status, [ArticleClosureBatchStatus::Pending, ArticleClosureBatchStatus::Failed], true)
                    || $batch->attempts >= config('article-closure.max_attempts')
                    || ($batch->next_attempt_at && $batch->next_attempt_at->isFuture())
                    || ($batch->dispatch_lease_until && $batch->dispatch_lease_until->isFuture())
                    || ! $batch->run->finalized_at) {
                    return false;
                }
                $batch->update(['dispatch_lease_until' => now()->addSeconds(config('article-closure.dispatch_lease'))]);

                return true;
            });
            if (! $claimed) {
                continue;
            }
            try {
                Bus::dispatch(new SendArticleClosureDigest($batchId));
            } catch (Throwable $error) {
                ArticleClosureBatch::query()->whereKey($batchId)
                    ->whereIn('status', [ArticleClosureBatchStatus::Pending, ArticleClosureBatchStatus::Failed])
                    ->update(['last_error' => 'Queue dispatch failed: '.Str::limit($error->getMessage(), 1500)]);
                Log::warning('Article closure digest dispatch failed; dispatch lease will expire for recovery', ['batch_id' => $batchId]);
            }
        }
    }
}
