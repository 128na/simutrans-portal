<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Tests\Feature\TestCase;

final class LoadArticleTest extends TestCase
{
    private ArticleRepository $articleRepository;

    private Article $article;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);
        $this->article = Article::factory()->create();
    }

    public function test(): void
    {
        $article = $this->articleRepository->loadArticle($this->article);

        $this->assertInstanceOf(Article::class, $article);
    }
}
