<?php

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Category;
use App\Repositories\ArticleRepository;
use Illuminate\Support\Collection;
use Tests\ArticleTestCase;

class findAllByPakTest extends ArticleTestCase
{
    private ArticleRepository $repository;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);

        $this->category = Category::where('type', 'pak')->first();
        $this->article->categories()->sync([$this->category->id]);
    }

    public function test()
    {
        $res = $this->repository->findAllByPak($this->category->slug);

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(1, $res->count(), 'カテゴリに紐づく記事のみ取得出来ること');
    }

    public function test公開以外のステータス()
    {
        $this->article->update(['status' => 'draft']);
        $res = $this->repository->findAllByPak($this->category->slug);

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(0, $res->count(), '非公開記事は取得できないこと');
    }

    public function test論理削除()
    {
        $this->article->delete();
        $res = $this->repository->findAllByPak($this->category->slug);

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(0, $res->count(), '削除済み記事は取得できないこと');
    }
}
