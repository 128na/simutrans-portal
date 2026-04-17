<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Article;
use App\Repositories\Article\FrontArticleRepository;
use Illuminate\Support\Collection;

/**
 * feed item はCollectionしか受け付けないのでここで変換する
 */
class FeedService
{
    public function __construct(
        private FrontArticleRepository $articleRepository,
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
        return $this->articleRepository->paginateLatest($pak)->getCollection();
    }

    /**
     * @return Collection<int,Article>
     */
    public function page(): Collection
    {
        return $this->articleRepository->paginatePages()->getCollection();
    }

    /**
     * @return Collection<int,Article>
     */
    public function announce(): Collection
    {
        return $this->articleRepository->paginateAnnounces()->getCollection();
    }
}
