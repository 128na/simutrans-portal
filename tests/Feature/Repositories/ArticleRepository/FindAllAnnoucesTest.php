<?php

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Repositories\ArticleRepository;
use Illuminate\Support\Collection;
use Tests\ArticleTestCase;

class FindAllAnnoucesTest extends ArticleTestCase
{
    private ArticleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
    }

    public function test()
    {
        $this->createMarkdownAnnounce();
        $res = $this->repository->findAllAnnouces();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(1, $res->count(), 'お知らせ記事のみ取得出来ること');
    }

    public function test公開以外のステータス()
    {
        $article = $this->createMarkdownAnnounce();
        $article->update(['status' => 'draft']);
        $res = $this->repository->findAllAnnouces();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(0, $res->count(), '非公開記事は取得できないこと');
    }

    public function test論理削除()
    {
        $article = $this->createMarkdownAnnounce();
        $article->delete();
        $res = $this->repository->findAllAnnouces();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(0, $res->count(), '削除済み記事は取得できないこと');
    }
}
