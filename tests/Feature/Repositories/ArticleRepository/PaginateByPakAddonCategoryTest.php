<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Category;
use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\ArticleTestCase;

final class PaginateByPakAddonCategoryTest extends ArticleTestCase
{
    private ArticleRepository $repository;

    private Category $pak;

    private Category $addon;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);

        $this->pak = Category::where('type', 'pak')->first();
        $this->addon = Category::where('type', 'addon')->first();
        $this->article->categories()->sync([$this->pak->id, $this->addon->id]);
    }

    public function test(): void
    {
        tap($this->createAddonIntroduction(), function ($a) {
            $a->categories()->sync([$this->pak->id]);
        });
        tap($this->createAddonIntroduction(), function ($a) {
            $a->categories()->sync([$this->addon->id]);
        });

        $res = $this->repository->paginateByPakAddonCategory($this->pak, $this->addon);

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEquals(1, $res->count(), 'pak,addonカテゴリ両方に紐づく記事のみ取得出来ること');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => 'draft']);
        $res = $this->repository->paginateByPakAddonCategory($this->pak, $this->addon);

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEquals(0, $res->count(), '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $res = $this->repository->paginateByPakAddonCategory($this->pak, $this->addon);

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEquals(0, $res->count(), '削除済み記事は取得できないこと');
    }
}
