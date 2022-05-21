<?php

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Tests\ArticleTestCase;

class PaginateRankingTest extends ArticleTestCase
{
    private ArticleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);

        DB::table('rankings')->insert([
            ['article_id' => $this->article->id, 'order' => 0],
            ['article_id' => $this->article2->id, 'order' => 1],
        ]);
    }

    public function test()
    {
        $res = $this->repository->paginateRanking([$this->article2->id]);

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEquals(1, $res->count(), '除外記事を除いた記事のみ取得出来ること');
    }

    public function test公開以外のステータス()
    {
        $this->article->update(['status' => 'draft']);
        $res = $this->repository->paginateRanking();

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEquals(1, $res->count(), '非公開記事は取得できないこと');
    }

    public function test論理削除()
    {
        $this->article->delete();
        $res = $this->repository->paginateRanking();

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEquals(1, $res->count(), '削除済み記事は取得できないこと');
    }
}
