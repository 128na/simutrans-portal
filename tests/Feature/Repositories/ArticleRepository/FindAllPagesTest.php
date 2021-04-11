<?php

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Repositories\ArticleRepository;
use Illuminate\Support\Collection;
use Tests\ArticleTestCase;

class FindAllPagesTest extends ArticleTestCase
{
    private ArticleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
    }

    public function test()
    {
        $this->createPage();
        $res = $this->repository->findAllPages();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(1, $res->count(), '一般記事のみ取得出来ること');
    }

    public function test公開以外のステータス()
    {
        $article = $this->createPage();
        $article->update(['status' => 'draft']);
        $res = $this->repository->findAllPages();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(0, $res->count(), '非公開記事は取得できないこと');
    }

    public function test論理削除()
    {
        $article = $this->createPage();
        $article->delete();
        $res = $this->repository->findAllPages();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(0, $res->count(), '削除済み記事は取得できないこと');
    }
}
