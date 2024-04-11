<?php

declare(strict_types=1);

namespace App\Actions\DeadLink;

use App\Enums\ArticleStatus;
use App\Events\Article\CloseByDeadLinkDetected;
use App\Models\Article;
use App\Repositories\ArticleRepository;

final readonly class OnDead
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private FailedCountCache $failedCountCache,
    ) {
    }

    public function __invoke(Article $article): bool
    {
        $count = 1 + $this->failedCountCache->get($article);
        if ($count < 3) {
            $this->failedCountCache->update($article, $count);

            return false;
        }

        $this->closeArticle($article);
        $this->failedCountCache->clear($article);

        return true;
    }

    private function closeArticle(Article $article): void
    {
        $this->articleRepository->update($article, ['status' => ArticleStatus::Private]);
        CloseByDeadLinkDetected::dispatch($article);
    }
}
