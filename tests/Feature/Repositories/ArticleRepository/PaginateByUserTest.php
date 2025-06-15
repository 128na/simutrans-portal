<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\Feature\TestCase;

final class PaginateByUserTest extends TestCase
{
    private ArticleRepository $articleRepository;

    private Article $article;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);
        $this->article = Article::factory()->publish()->create();
    }

    public function test(): void
    {
        Article::factory()->publish()->create();
        $lengthAwarePaginator = $this->articleRepository->paginateByUser($this->article->user);

        $this->assertInstanceOf(LengthAwarePaginator::class, $lengthAwarePaginator);
        $this->assertCount(1, $lengthAwarePaginator, 'カテゴリに紐づく記事のみ取得出来ること');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => ArticleStatus::Draft]);
        $lengthAwarePaginator = $this->articleRepository->paginateByUser($this->article->user);

        $this->assertInstanceOf(LengthAwarePaginator::class, $lengthAwarePaginator);
        $this->assertEmpty($lengthAwarePaginator, '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $lengthAwarePaginator = $this->articleRepository->paginateByUser($this->article->user);

        $this->assertInstanceOf(LengthAwarePaginator::class, $lengthAwarePaginator);
        $this->assertEmpty($lengthAwarePaginator, '削除済み記事は取得できないこと');
    }
}
