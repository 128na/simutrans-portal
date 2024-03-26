<?php

declare(strict_types=1);

namespace Tests\OldFeature\Repositories\ArticleRepository;

use App\Models\Tag;
use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\ArticleTestCase;

class PaginateByTagTest extends ArticleTestCase
{
    private ArticleRepository $repository;

    private Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);

        $this->tag = Tag::factory()->create();
        $this->article->tags()->sync([$this->tag->id]);
    }

    public function test(): void
    {
        $res = $this->repository->paginateByTag($this->tag);

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEquals(1, $res->count(), 'カテゴリに紐づく記事のみ取得出来ること');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => 'draft']);
        $res = $this->repository->paginateByTag($this->tag);

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEquals(0, $res->count(), '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $res = $this->repository->paginateByTag($this->tag);

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEquals(0, $res->count(), '削除済み記事は取得できないこと');
    }
}
