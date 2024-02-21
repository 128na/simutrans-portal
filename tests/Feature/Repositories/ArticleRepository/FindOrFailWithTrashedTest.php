<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\ArticleTestCase;

class FindOrFailWithTrashedTest extends ArticleTestCase
{
    private ArticleRepository $articleRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        $res = $this->articleRepository->findOrFailWithTrashed($this->article->id);

        $this->assertInstanceOf(Article::class, $res);
    }

    public function testNotFound(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->articleRepository->findOrFailWithTrashed(0);
    }
}
