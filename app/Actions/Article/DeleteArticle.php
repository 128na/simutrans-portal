<?php

declare(strict_types=1);

namespace App\Actions\Article;

use App\Events\Article\ArticleDeleted;
use App\Models\Article;
use App\Repositories\ArticleRepository;

class DeleteArticle
{
    public function __construct(
        private ArticleRepository $articleRepository,
    ) {}

    public function __invoke(Article $article): void
    {
        $this->articleRepository->destroy($article);

        event(new ArticleDeleted($article));
    }
}
