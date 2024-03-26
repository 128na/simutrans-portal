<?php

declare(strict_types=1);

namespace Tests\OldFeature\Repositories\ArticleRepository;

use App\Repositories\ArticleRepository;
use Illuminate\Support\LazyCollection;
use Tests\ArticleTestCase;

class CursorCheckLinkTest extends ArticleTestCase
{
    private ArticleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        $this->createAddonPost();
        $this->createPage();
        $this->createMarkdown();
        $this->createMarkdownAnnounce();

        $lazyCollection = $this->repository->cursorCheckLink();

        $this->assertInstanceOf(LazyCollection::class, $lazyCollection);
        $this->assertEquals(2, $lazyCollection->count(), 'アドオン紹介記事のみ取得できること');
    }

    public function testチェック無効(): void
    {
        $contents = $this->article->contents;
        $contents->exclude_link_check = true;
        $this->article->update([
            'contents' => $contents,
        ]);

        $res = $this->repository->cursorCheckLink();

        $this->assertInstanceOf(LazyCollection::class, $res);
        $this->assertEquals(1, $res->count(), 'チェック無効の記事は取得できないこと');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => 'draft']);
        $lazyCollection = $this->repository->cursorCheckLink();

        $this->assertInstanceOf(LazyCollection::class, $lazyCollection);
        $this->assertEquals(1, $lazyCollection->count(), '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $lazyCollection = $this->repository->cursorCheckLink();

        $this->assertInstanceOf(LazyCollection::class, $lazyCollection);
        $this->assertEquals(1, $lazyCollection->count(), '削除済み記事は取得できないこと');
    }
}
