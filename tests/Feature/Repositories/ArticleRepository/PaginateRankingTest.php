<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Article\Ranking;
use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\Feature\TestCase;

final class PaginateRankingTest extends TestCase
{
    private Article $article;

    private ArticleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->article = Article::factory()->publish()->create();
        Ranking::create(['rank' => 1, 'article_id' => $this->article->id]);
        $this->repository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        Article::factory()->publish()->create();
        $paginator = $this->repository->paginateRanking();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertCount(1, $paginator->items(), 'ランキングリレーションのある記事のみ取得できること');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => ArticleStatus::Draft]);
        $paginator = $this->repository->paginateRanking();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertCount(0, $paginator->items(), '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $paginator = $this->repository->paginateRanking();

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertCount(0, $paginator->items(), '削除済み記事は取得できないこと');
    }
}
