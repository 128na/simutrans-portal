<?php

declare(strict_types=1);

namespace Tests\OldFeature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\ArticleTestCase;

class FindOrFailWithTrashedTest extends ArticleTestCase
{
    private ArticleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        $article = $this->repository->findOrFailWithTrashed($this->article->id);

        $this->assertInstanceOf(Article::class, $article);
    }

    public function testNotFound(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFailWithTrashed(0);
    }
}
