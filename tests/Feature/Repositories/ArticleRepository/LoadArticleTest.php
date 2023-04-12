<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Tests\ArticleTestCase;

final class LoadArticleTest extends ArticleTestCase
{
    private ArticleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        $res = $this->repository->loadArticle($this->article);

        $this->assertInstanceOf(Article::class, $res);
    }
}
