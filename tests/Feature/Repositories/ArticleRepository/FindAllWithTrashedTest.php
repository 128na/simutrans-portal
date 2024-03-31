<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Support\Collection;
use Tests\Feature\TestCase;

class FindAllWithTrashedTest extends TestCase
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
        $res = $this->repository->findAllWithTrashed();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(1, $res->count(), '全ての記事が取得できること');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => ArticleStatus::Draft]);
        $res = $this->repository->findAllWithTrashed();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(1, $res->count(), '非公開記事も取得できること');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $res = $this->repository->findAllWithTrashed();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(1, $res->count(), '削除済み記事も取得できること');
    }
}
