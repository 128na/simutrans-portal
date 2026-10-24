<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\ArticleClosure\Recover;
use App\Enums\ArticleClosureBatchStatus;
use App\Jobs\SendArticleClosureDigest;
use App\Models\ArticleClosureBatch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArticleClosureNotifications extends Command
{
    protected $signature = 'article:closure-notifications {--recover : Recover unfinished runs and due deliveries} {--status : Show the latest delivery states} {--retry= : Explicitly resend a failed or unknown batch ID; unknown may have already been delivered}';

    protected $description = 'Recover pending article closure digests, inspect delivery states, or explicitly retry one batch';

    public function handle(Recover $recover): int
    {
        if ($this->option('retry') !== null) {
            $id = filter_var($this->option('retry'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($id === false) {
                $this->error('A positive batch ID is required.');

                return self::FAILURE;
            }
            $reset = DB::transaction(function () use ($id): bool {
                $batch = ArticleClosureBatch::query()->lockForUpdate()->find($id);
                if (! $batch || ! $batch->run->finalized_at
                    || ! in_array($batch->status, [ArticleClosureBatchStatus::Failed, ArticleClosureBatchStatus::Unknown], true)) {
                    return false;
                }
                Log::notice('Article closure digest manually retried; unknown outcomes may duplicate delivery', ['batch_id' => $id, 'previous_status' => $batch->status->value]);
                $batch->update(['status' => ArticleClosureBatchStatus::Pending, 'attempts' => 0, 'dispatch_lease_until' => null, 'next_attempt_at' => null,
                    'sending_at' => null, 'sending_token' => null, 'last_error' => null]);

                return true;
            });
            if (! $reset) {
                $this->error('Only failed or unknown batches belonging to finalized runs can be retried.');

                return self::FAILURE;
            }
            SendArticleClosureDigest::dispatch($id);
            $this->info('Retry queued. Unknown outcomes may result in duplicate delivery.');

            return self::SUCCESS;
        }

        if ($this->option('status')) {
            $this->table(['ID', 'Run', 'User', 'Status', 'Attempts', 'Next attempt', 'Sent', 'Last error'],
                ArticleClosureBatch::query()->orderByDesc('id')->limit(100)->get()->map(fn (ArticleClosureBatch $batch): array => [
                    $batch->id, $batch->run_id, $batch->user_id, $batch->status->value, $batch->attempts,
                    $batch->next_attempt_at?->toDateTimeString(), $batch->sent_at?->toDateTimeString(), $batch->last_error,
                ])->all());

            return self::SUCCESS;
        }

        $recover();

        return self::SUCCESS;
    }
}
