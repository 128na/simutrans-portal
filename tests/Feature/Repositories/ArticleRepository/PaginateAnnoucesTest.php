<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\ArticleTestCase;

class PaginateAnnoucesTest extends ArticleTestCase
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

        $res = $this->articleRepository->paginateAnnouces();

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEquals(1, $res->count(), 'お知らせ記事のみ取得できること');
    }

    public function test公開以外のステータス(): void
    {
        $article = $this->createMarkdownAnnounce();
        $article->update(['status' => 'draft']);

        $res = $this->articleRepository->paginateAnnouces();

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEquals(0, $res->count(), '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $article = $this->createMarkdownAnnounce();
        $article->delete();

        $res = $this->articleRepository->paginateAnnouces();

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEquals(0, $res->count(), '削除済み記事は取得できないこと');
    }
}
