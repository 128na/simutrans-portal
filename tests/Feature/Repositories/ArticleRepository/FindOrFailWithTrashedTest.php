<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Feature\TestCase;

class FindOrFailWithTrashedTest extends TestCase
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
        $article = $this->repository->findOrFailWithTrashed($this->article->id);

        $this->assertInstanceOf(Article::class, $article);
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => ArticleStatus::Draft]);
        $article = $this->repository->findOrFailWithTrashed($this->article->id);

        $this->assertInstanceOf(Article::class, $article);
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $article = $this->repository->findOrFailWithTrashed($this->article->id);

        $this->assertInstanceOf(Article::class, $article);
    }

    public function testNotFound(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFailWithTrashed(0);
    }
}
