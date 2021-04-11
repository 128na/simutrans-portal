<?php

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Repositories\ArticleRepository;
use Illuminate\Support\Collection;
use Tests\ArticleTestCase;

class FindAllFeedItemsTest extends ArticleTestCase
{
    private ArticleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
    }

    public function test()
    {
        $this->createAddonPost();

        $res = $this->repository->findAllFeedItems();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(3, $res->count(), 'アドオン公開・紹介記事のみ取得できること');
    }

    public function test公開以外のステータス()
    {
        $this->article->update(['status' => 'draft']);
        $res = $this->repository->findAllFeedItems();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(1, $res->count(), '非公開記事は取得できないこと');
    }

    public function test論理削除()
    {
        $this->article->delete();
        $res = $this->repository->findAllFeedItems();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(1, $res->count(), '削除済み記事は取得できないこと');
    }
}
