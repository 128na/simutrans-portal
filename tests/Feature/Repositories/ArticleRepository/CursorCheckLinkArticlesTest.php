<?php

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Repositories\ArticleRepository;
use Illuminate\Support\LazyCollection;
use Tests\ArticleTestCase;

class CursorCheckLinkArticlesTest extends ArticleTestCase
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
        $this->createPage();
        $this->createMarkdown();
        $this->createMarkdownAnnounce();

        $res = $this->repository->cursorCheckLinkArticles();

        $this->assertInstanceOf(LazyCollection::class, $res);
        $this->assertEquals(1, $res->count(), 'アドオン紹介記事のみ取得できること');
    }

    public function testチェック無効()
    {
        $contents = $this->article->contents;
        $contents->exclude_link_check = true;
        $this->article->update([
            'contents' => $contents,
        ]);

        $res = $this->repository->cursorCheckLinkArticles();

        $this->assertInstanceOf(LazyCollection::class, $res);
        $this->assertEquals(0, $res->count(), 'チェック無効の記事は取得できないこと');
    }

    public function test公開以外のステータス()
    {
        $this->article->update(['status' => 'draft']);
        $res = $this->repository->cursorCheckLinkArticles();

        $this->assertInstanceOf(LazyCollection::class, $res);
        $this->assertEquals(0, $res->count(), '非公開記事は取得できないこと');
    }

    public function test論理削除()
    {
        $this->article->delete();
        $res = $this->repository->cursorCheckLinkArticles();

        $this->assertInstanceOf(LazyCollection::class, $res);
        $this->assertEquals(0, $res->count(), '削除済み記事は取得できないこと');
    }
}
