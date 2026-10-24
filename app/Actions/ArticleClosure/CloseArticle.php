<?php

declare(strict_types=1);

namespace App\Actions\ArticleClosure;

use App\Actions\DeadLink\InIgnoreList;
use App\Enums\ArticleClosureBatchStatus;
use App\Enums\ArticleClosureReason;
use App\Enums\ArticleClosureRunStatus;
use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Models\ArticleClosureBatch;
use App\Models\ArticleClosureItem;
use App\Models\ArticleClosureRun;
use App\Models\Contents\AddonIntroductionContent;
use App\Models\User;
use App\Repositories\ArticleLinkCheckHistoryRepository;
use App\Repositories\ArticleRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;

class CloseArticle
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private ArticleLinkCheckHistoryRepository $historyRepository,
        private InIgnoreList $inIgnoreList,
        private Logger $logger,
    ) {}

    public function __invoke(Article $expected, int $runId, ArticleClosureReason $reason): bool
    {
        return DB::transaction(function () use ($expected, $runId, $reason): bool {
            $run = ArticleClosureRun::query()->lockForUpdate()->findOrFail($runId);
            if ($run->status !== ArticleClosureRunStatus::Running) {
                return false;
            }
            $current = Article::query()->lockForUpdate()->find($expected->id);
            if (! $current || ! $this->isEligible($current, $expected, $reason)) {
                return false;
            }
            if (ArticleClosureItem::where('article_id', $current->id)
                ->whereHas('batch', fn (Builder $query) => $query->where('run_id', $run->id))->exists()) {
                return false;
            }
            $batch = ArticleClosureBatch::firstOrCreate(['run_id' => $run->id, 'user_id' => $current->user_id],
                ['status' => ArticleClosureBatchStatus::Pending]);

            /** @var AddonIntroductionContent $contents */
            $contents = $current->contents;
            $this->articleRepository->update($current, ['status' => ArticleStatus::Private]);
            $this->historyRepository->clear($current);
            $item = ArticleClosureItem::create([
                'batch_id' => $batch->id, 'article_id' => $current->id,
                'title' => $current->title, 'url' => $contents->link,
                'edit_url' => route('mypage.articles.edit', ['article' => $current->id]),
                'reason' => $reason, 'closed_at' => now(),
            ]);
            $run->update(['heartbeat_at' => now()]);
            DB::afterCommit(function () use ($current, $item): void {
                dispatch(new JobUpdateRelated($current->id));
                $this->logger->channel('audit')->info('記事非公開化', [
                    ...$current->getInfoLogging(), 'reason' => $item->reason->value,
                    'closureItemId' => $item->id,
                ]);
            });

            return true;
        });
    }

    public function isEligible(Article $current, Article $expected, ArticleClosureReason $reason): bool
    {
        if ($current->id !== $expected->id || $current->user_id !== $expected->user_id
            || ! $current->is_publish || $current->trashed()
            || $current->post_type !== ArticlePostType::AddonIntroduction
            || ! User::query()->whereKey($current->user_id)->exists()
            || ! $current->contents instanceof AddonIntroductionContent
            || ! $expected->contents instanceof AddonIntroductionContent) {
            return false;
        }
        $link = $current->contents->link;
        if (in_array($link, [null, '', '0'], true) || $link !== $expected->contents->link) {
            return false;
        }

        return $reason === ArticleClosureReason::ServiceClosed
            || (! $current->contents->exclude_link_check && ! ($this->inIgnoreList)($link));
    }
}
