<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Category;
use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\Feature\TestCase;

final class PaginateByCategoryTest extends TestCase
{
    private ArticleRepository $repository;

    private Category $category;

    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);

        $this->category = Category::factory()->create();
        $this->article = Article::factory()->publish()->create();
        $this->article->categories()->sync([$this->category->id]);
    }

    public function test(): void
    {
        Article::factory()->publish()->create();
        $paginator = $this->repository->paginateByCategory($this->category);

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertCount(1, $paginator->items(), 'カテゴリに紐づく記事のみ取得出来ること');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => ArticleStatus::Draft]);
        $paginator = $this->repository->paginateByCategory($this->category);

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertCount(0, $paginator->items(), '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $paginator = $this->repository->paginateByCategory($this->category);

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertCount(0, $paginator->items(), '削除済み記事は取得できないこと');
    }
}
