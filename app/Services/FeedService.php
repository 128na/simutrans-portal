<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Article;
use App\Repositories\v2\ArticleRepository;
use Illuminate\Support\Collection;

/**
 * feed item はCollectionしか受け付けないのでここで変換する
 */
final readonly class FeedService
{
    public function __construct(
        private ArticleRepository $articleRepository,
    ) {}

    /**
     * @return Collection<int,Article>
     */
    public function pakAll(): Collection
    {
        return $this->articleRepository->getLatestAllPak()->getCollection();
    }

    /**
     * @return Collection<int,Article>
     */
    public function latestPak(string $pak): Collection
    {
        return $this->articleRepository->getLatest($pak)->getCollection();
    }

    /**
     * @return Collection<int,Article>
     */
    public function page(): Collection
    {
        return $this->articleRepository->getPages()->getCollection();
    }

    /**
     * @return Collection<int,Article>
     */
    public function announce(): Collection
    {
        return $this->articleRepository->getAnnounces()->getCollection();
    }
}
