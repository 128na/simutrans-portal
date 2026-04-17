<?php

declare(strict_types=1);

namespace App\Actions\FrontArticle;

use App\Models\Article;
use App\Repositories\Article\FrontArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class LatestAction
{
    public function __construct(private FrontArticleRepository $articleRepository) {}

    /**
     * @return LengthAwarePaginator<int, Article>
     */
    public function allPak(int $limit = 24): LengthAwarePaginator
    {
        return $this->articleRepository->getLatestAllPak($limit);
    }

    /**
     * @return LengthAwarePaginator<int, Article>
     */
    public function byPak(string $pak, int $limit = 24): LengthAwarePaginator
    {
        return $this->articleRepository->paginateLatest($pak, $limit);
    }

    /**
     * @return LengthAwarePaginator<int, Article>
     */
    public function others(int $limit = 24): LengthAwarePaginator
    {
        return $this->articleRepository->getLatestOther($limit);
    }
}
