<?php

declare(strict_types=1);

namespace App\Actions\DeadLink;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Repositories\ArticleLinkCheckHistoryRepository;
use App\Repositories\ArticleRepository;

class OnDead
{
    private const int FAILED_LIMIT = 3;

    public function __construct(
        private ArticleRepository $articleRepository,
        private ArticleLinkCheckHistoryRepository $articleLinkCheckHistoryRepository,
    ) {}

    public function __invoke(Article $article): bool
    {
        event(new \App\Events\Article\DeadLinkDetected($article));

        $this->articleLinkCheckHistoryRepository->increment($article);
        $count = $this->articleLinkCheckHistoryRepository->get($article);

        if ($count < self::FAILED_LIMIT) {
            return false;
        }

        $this->articleRepository->update($article, ['status' => ArticleStatus::Private]);

        event(new \App\Events\Article\CloseByDeadLinkDetected($article));
        $this->articleLinkCheckHistoryRepository->clear($article);

        return true;
    }
}
