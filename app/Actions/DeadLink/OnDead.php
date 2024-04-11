<?php

declare(strict_types=1);

namespace App\Actions\DeadLink;

use App\Enums\ArticleStatus;
use App\Events\Article\CloseByDeadLinkDetected;
use App\Events\Article\DeadLinkDetected;
use App\Models\Article;
use App\Repositories\ArticleRepository;

final readonly class OnDead
{
    private const int FAILED_LIMIT = 3;

    public function __construct(
        private ArticleRepository $articleRepository,
        private FailedCountCache $failedCountCache,
    ) {
    }

    public function __invoke(Article $article): bool
    {
        DeadLinkDetected::dispatch($article);
        $count = 1 + $this->failedCountCache->get($article);
        if ($count < self::FAILED_LIMIT) {
            $this->failedCountCache->update($article, $count);

            return false;
        }

        $this->articleRepository->update($article, ['status' => ArticleStatus::Private]);

        CloseByDeadLinkDetected::dispatch($article);
        $this->failedCountCache->clear($article);

        return true;
    }
}
