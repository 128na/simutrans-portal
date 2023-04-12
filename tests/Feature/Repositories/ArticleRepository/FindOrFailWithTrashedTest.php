<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\ArticleTestCase;

final class FindOrFailWithTrashedTest extends ArticleTestCase
{
    private ArticleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        $res = $this->repository->findOrFailWithTrashed($this->article->id);

        $this->assertInstanceOf(Article::class, $res);
    }

    public function testNotFound(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFailWithTrashed(0);
    }
}
