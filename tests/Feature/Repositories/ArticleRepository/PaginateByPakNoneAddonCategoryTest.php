<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleStatus;
use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Category;
use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\Feature\TestCase;

final class PaginateByPakNoneAddonCategoryTest extends TestCase
{
    private ArticleRepository $articleRepository;

    private Article $article;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);

        $this->category = Category::factory()->create(['type' => CategoryType::Pak]);
        $this->article = Article::factory()->publish()->create();
        $this->article->categories()->sync([$this->category->id]);
    }

    public function test(): void
    {
        $addon = Category::factory()->create(['type' => CategoryType::Addon]);
        tap(Article::factory()->create(), fn ($a) => $a->categories()->sync([$this->category->id, $addon->id]));

        $res = $this->articleRepository->paginateByPakNoneAddonCategory($this->category);
        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertCount(1, $res->items(), 'pak,addonカテゴリ両方に紐づく記事のみ取得出来ること');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => ArticleStatus::Draft]);
        $res = $this->articleRepository->paginateByPakNoneAddonCategory($this->category);

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertCount(0, $res->items(), '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $res = $this->articleRepository->paginateByPakNoneAddonCategory($this->category);

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertCount(0, $res->items(), '削除済み記事は取得できないこと');
    }
}
