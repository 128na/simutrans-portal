<?php

namespace App\Services\GoogleAnalytics;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Support\Collection;

class ResolveArticleService
{
    public function __construct(private ArticleRepository $articleRepository)
    {
    }

    /**
     * @return Collection<Article>
     */
    public function pathToArticles(array $pathes): Collection
    {
        $slugs = array_map(fn ($path) => urlencode(str_replace('/articles/', '', $path)), $pathes);

        return $this->articleRepository->findBySlugs($slugs);
    }
}
