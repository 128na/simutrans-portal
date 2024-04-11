<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\Feature\TestCase;

final class PaginateAnnoucesTest extends TestCase
{
    private Article $article;

    private ArticleRepository $articleRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->article = $this->createAnnounce();
        $this->articleRepository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        Article::factory()->page()->create();
        $paginator = $this->articleRepository->paginateAnnouces();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertCount(1, $paginator->items(), 'お知らせ記事のみ取得できること');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => ArticleStatus::Draft]);

        $paginator = $this->articleRepository->paginateAnnouces();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertCount(0, $paginator->items(), '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $this->article->delete();

        $paginator = $this->articleRepository->paginateAnnouces();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertCount(0, $paginator->items(), '削除済み記事は取得できないこと');
    }
}
