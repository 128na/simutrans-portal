<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Tests\ArticleTestCase;

class LoadArticleTest extends ArticleTestCase
{
    private ArticleRepository $articleRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        $res = $this->articleRepository->loadArticle($this->article);

        $this->assertInstanceOf(Article::class, $res);
    }
}
