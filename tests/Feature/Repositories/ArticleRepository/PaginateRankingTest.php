<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article\Ranking;
use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\ArticleTestCase;

class PaginateRankingTest extends ArticleTestCase
{
    private ArticleRepository $articleRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);
        Ranking::create(['rank' => 1, 'article_id' => $this->article->id]);
    }

    public function test(): void
    {
        $this->createPage();
        $res = $this->articleRepository->paginateRanking();

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEquals(1, $res->count());
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => 'draft']);
        $res = $this->articleRepository->paginateRanking();

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEquals(0, $res->count(), '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $res = $this->articleRepository->paginateRanking();

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEquals(0, $res->count(), '削除済み記事は取得できないこと');
    }
}
