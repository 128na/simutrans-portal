<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Tests\Feature\TestCase;

class FindRandomPRTest extends TestCase
{
    private ArticleRepository $repository;

    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
        $this->article = Article::factory()->publish()->create(['pr' => true]);
    }

    public function test(): void
    {
        $res = $this->repository->findRandomPR();

        $this->assertInstanceOf(Article::class, $res);
    }

    public function testPR以外(): void
    {
        $this->article->update(['pr' => false]);
        $res = $this->repository->findRandomPR();

        $this->assertNull($res);
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => ArticleStatus::Draft]);
        $res = $this->repository->findRandomPR();

        $this->assertNull($res);
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $res = $this->repository->findRandomPR();

        $this->assertNull($res);
    }
}
