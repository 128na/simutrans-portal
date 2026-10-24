<?php

declare(strict_types=1);

namespace App\Actions\DeadLink;

use App\Actions\ArticleClosure\CloseArticle;
use App\Enums\ArticleClosureReason;
use App\Enums\ArticleClosureRunStatus;
use App\Models\Article;
use App\Models\ArticleClosureRun;
use App\Repositories\ArticleLinkCheckHistoryRepository;
use Illuminate\Support\Facades\DB;

class ProcessDeadLink
{
    public function __construct(
        private CloseArticle $closeArticle,
        private OnDead $onDead,
        private ArticleLinkCheckHistoryRepository $history,
    ) {}

    public function __invoke(Article $expected, int $runId): bool
    {
        return $this->process($expected, $runId, false);
    }

    public function alive(Article $expected, int $runId): void
    {
        $this->process($expected, $runId, true);
    }

    private function process(Article $expected, int $runId, bool $alive): bool
    {
        return DB::transaction(function () use ($expected, $runId, $alive): bool {
            $run = ArticleClosureRun::query()->lockForUpdate()->findOrFail($runId);
            if ($run->status !== ArticleClosureRunStatus::Running) {
                return false;
            }
            $article = Article::query()->lockForUpdate()->find($expected->id);
            if (! $article || ! $this->closeArticle->isEligible($article, $expected, ArticleClosureReason::DeadLink)) {
                return false;
            }
            $run->update(['heartbeat_at' => now()]);
            if ($alive) {
                $this->history->clear($article);

                return false;
            }

            return ($this->onDead)($article, $runId);
        });
    }
}
