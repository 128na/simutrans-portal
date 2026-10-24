<?php

declare(strict_types=1);

namespace App\Actions\ArticleClosure;

use App\Enums\ArticleClosureReason;
use App\Enums\ArticleClosureRunStatus;
use App\Models\ArticleClosureRun;
use Illuminate\Support\Facades\DB;

class Runs
{
    public function start(ArticleClosureReason $reason = ArticleClosureReason::DeadLink): ArticleClosureRun
    {
        return ArticleClosureRun::create(['kind' => $reason, 'status' => ArticleClosureRunStatus::Running,
            'started_at' => now(), 'heartbeat_at' => now()]);
    }

    public function heartbeat(int $runId): bool
    {
        return DB::transaction(function () use ($runId): bool {
            $run = ArticleClosureRun::query()->lockForUpdate()->findOrFail($runId);
            if ($run->status !== ArticleClosureRunStatus::Running) {
                return false;
            }
            $run->update(['heartbeat_at' => now()]);

            return true;
        });
    }

    public function finalize(int $runId, bool $interrupted = false): void
    {
        DB::transaction(function () use ($runId, $interrupted): void {
            $run = ArticleClosureRun::query()->lockForUpdate()->findOrFail($runId);
            if ($run->status !== ArticleClosureRunStatus::Running) {
                return;
            }
            $run->update(['status' => $interrupted ? ArticleClosureRunStatus::Interrupted : ArticleClosureRunStatus::Completed,
                'finalized_at' => now()]);
        });
    }
}
