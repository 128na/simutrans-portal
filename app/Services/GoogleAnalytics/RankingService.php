<?php

namespace App\Services\GoogleAnalytics;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Support\Collection;

class RankingService
{
    public function __construct(private ArticleRepository $articleRepository)
    {
    }

    /**
     * @return Collection<Article>
     */
    public function updateRanking(Collection $articles): void
    {
        $this->articleRepository->updateRanking($articles);
    }
}
