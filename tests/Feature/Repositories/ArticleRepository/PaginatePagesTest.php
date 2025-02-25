<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\Feature\TestCase;

final class PaginatePagesTest extends TestCase
{
    private Article $article;

    private ArticleRepository $articleRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->article = Article::factory()->page()->publish()->create();
        $this->articleRepository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        Article::factory()->addonIntroduction()->create();
        $paginator = $this->articleRepository->paginatePages();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertCount(1, $paginator->items(), '一般記事のみ取得出来ること');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => ArticleStatus::Draft]);

        $paginator = $this->articleRepository->paginatePages();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEmpty($paginator->items(), '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $this->article->delete();

        $paginator = $this->articleRepository->paginatePages();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEmpty($paginator->items(), '削除済み記事は取得できないこと');
    }
}
