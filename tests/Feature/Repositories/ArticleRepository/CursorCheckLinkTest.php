<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Repositories\ArticleRepository;
use Illuminate\Support\LazyCollection;
use Tests\ArticleTestCase;

class CursorCheckLinkTest extends ArticleTestCase
{
    private ArticleRepository $articleRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        $this->createAddonPost();
        $this->createPage();
        $this->createMarkdown();
        $this->createMarkdownAnnounce();

        $res = $this->articleRepository->cursorCheckLink();

        $this->assertInstanceOf(LazyCollection::class, $res);
        $this->assertEquals(2, $res->count(), 'アドオン紹介記事のみ取得できること');
    }

    public function testチェック無効(): void
    {
        $contents = $this->article->contents;
        $contents->exclude_link_check = true;
        $this->article->update([
            'contents' => $contents,
        ]);

        $res = $this->articleRepository->cursorCheckLink();

        $this->assertInstanceOf(LazyCollection::class, $res);
        $this->assertEquals(1, $res->count(), 'チェック無効の記事は取得できないこと');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => 'draft']);
        $res = $this->articleRepository->cursorCheckLink();

        $this->assertInstanceOf(LazyCollection::class, $res);
        $this->assertEquals(1, $res->count(), '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $res = $this->articleRepository->cursorCheckLink();

        $this->assertInstanceOf(LazyCollection::class, $res);
        $this->assertEquals(1, $res->count(), '削除済み記事は取得できないこと');
    }
}
