<?php

declare(strict_types=1);

namespace App\Actions\Article;

use App\Enums\ArticleStatus;
use Carbon\CarbonImmutable;

final readonly class DecidePublishedAt
{
    public function __construct(
        private CarbonImmutable $now,
    ) {
    }

    public function __invoke(string $publishedAt, ArticleStatus $articleStatus): ?string
    {
        // 公開なら現在時刻
        if ($articleStatus === ArticleStatus::Publish) {
            return $this->now->toDateTimeString();
        }

        // 予約なら指定時刻
        if ($articleStatus === ArticleStatus::Reservation) {
            return $publishedAt;
        }

        // それ以外はまだ公開しない
        return null;
    }
}
