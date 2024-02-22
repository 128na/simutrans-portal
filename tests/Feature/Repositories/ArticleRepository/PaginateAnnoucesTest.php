<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\ArticleTestCase;

class PaginateAnnoucesTest extends ArticleTestCase
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

        $paginator = $this->repository->paginateAnnouces();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEquals(1, $paginator->count(), 'お知らせ記事のみ取得できること');
    }

    public function test公開以外のステータス(): void
    {
        $article = $this->createMarkdownAnnounce();
        $article->update(['status' => 'draft']);

        $paginator = $this->repository->paginateAnnouces();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEquals(0, $paginator->count(), '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $article = $this->createMarkdownAnnounce();
        $article->delete();

        $paginator = $this->repository->paginateAnnouces();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEquals(0, $paginator->count(), '削除済み記事は取得できないこと');
    }
}
