<?php

declare(strict_types=1);

namespace Tests\OldFeature\Repositories\ArticleRepository;

use App\Models\Article\Ranking;
use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\ArticleTestCase;

class PaginateRankingTest extends ArticleTestCase
{
    private ArticleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
        Ranking::create(['rank' => 1, 'article_id' => $this->article->id]);
    }

    public function test(): void
    {
        $this->createPage();
        $paginator = $this->repository->paginateRanking();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEquals(1, $paginator->count());
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => 'draft']);
        $paginator = $this->repository->paginateRanking();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEquals(0, $paginator->count(), '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $paginator = $this->repository->paginateRanking();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEquals(0, $paginator->count(), '削除済み記事は取得できないこと');
    }
}
