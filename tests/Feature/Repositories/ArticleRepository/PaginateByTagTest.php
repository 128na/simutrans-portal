<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Tag;
use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\Feature\TestCase;

final class PaginateByTagTest extends TestCase
{
    private ArticleRepository $articleRepository;

    private Tag $tag;

    private Article $article;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);

        $this->article = Article::factory()->publish()->create();
        $this->tag = Tag::factory()->create();
        $this->article->tags()->sync([$this->tag->id]);
    }

    public function test(): void
    {
        Article::factory()->publish()->create();
        $res = $this->articleRepository->paginateByTag($this->tag);

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertCount(1, $res, 'カテゴリに紐づく記事のみ取得出来ること');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => ArticleStatus::Draft]);
        $res = $this->articleRepository->paginateByTag($this->tag);

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEmpty($res, '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $res = $this->articleRepository->paginateByTag($this->tag);

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEmpty($res, '削除済み記事は取得できないこと');
    }
}
