<?php

declare(strict_types=1);

namespace App\Actions\Article;

use App\Events\Article\ArticleDeleted;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Services\Front\PublicArticleSnapshot;

class DeleteArticle
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private PublicArticleSnapshot $snapshot,
    ) {}

    public function __invoke(Article $article): void
    {
        $this->snapshot->mutate(function () use ($article): void {
            $this->snapshot->invalidate();
            $this->articleRepository->destroy($article);
        });

        event(new ArticleDeleted($article));
    }
}
