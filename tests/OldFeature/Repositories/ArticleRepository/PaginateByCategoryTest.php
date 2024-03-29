<?php

declare(strict_types=1);

namespace Tests\OldFeature\Repositories\ArticleRepository;

use App\Models\Category;
use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\ArticleTestCase;

class PaginateByCategoryTest extends ArticleTestCase
{
    private ArticleRepository $repository;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);

        $this->category = Category::first();
        $this->article->categories()->sync([$this->category->id]);
    }

    public function test(): void
    {
        /**
         * @var LengthAwarePaginator $paginator
         */
        $paginator = $this->repository->paginateByCategory($this->category);

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEquals(1, $paginator->count(), 'カテゴリに紐づく記事のみ取得出来ること');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => 'draft']);
        /**
         * @var LengthAwarePaginator $paginator
         */
        $paginator = $this->repository->paginateByCategory($this->category);

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEquals(0, $paginator->count(), '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        /**
         * @var LengthAwarePaginator $paginator
         */
        $paginator = $this->repository->paginateByCategory($this->category);

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEquals(0, $paginator->count(), '削除済み記事は取得できないこと');
    }
}
