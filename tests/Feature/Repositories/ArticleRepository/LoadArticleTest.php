<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Tests\Feature\TestCase;

class LoadArticleTest extends TestCase
{
    private ArticleRepository $repository;

    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
        $this->article = Article::factory()->create();
    }

    public function test(): void
    {
        $article = $this->repository->loadArticle($this->article);

        $this->assertInstanceOf(Article::class, $article);
    }
}
