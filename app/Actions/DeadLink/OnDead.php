<?php

declare(strict_types=1);

namespace App\Actions\DeadLink;

use App\Actions\ArticleClosure\CloseArticle;
use App\Enums\ArticleClosureReason;
use App\Events\Article\DeadLinkDetected;
use App\Models\Article;
use App\Repositories\ArticleLinkCheckHistoryRepository;

class OnDead
{
    private const int FAILED_LIMIT = 3;

    public function __construct(
        private CloseArticle $closeArticle,
        private ArticleLinkCheckHistoryRepository $articleLinkCheckHistoryRepository,
    ) {}

    public function __invoke(Article $article, int $runId): bool
    {
        event(new DeadLinkDetected($article));

        $this->articleLinkCheckHistoryRepository->increment($article);
        $count = $this->articleLinkCheckHistoryRepository->get($article);

        if ($count < self::FAILED_LIMIT) {
            return false;
        }

        return ($this->closeArticle)($article, $runId, ArticleClosureReason::DeadLink);
    }
}
