<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\LazyCollection;
use Tests\Feature\TestCase;

final class FetchAggregatedRankingTest extends TestCase
{
    private ArticleRepository $articleRepository;

    private Article $article1;

    private Article $article2;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);
        $this->article1 = Article::factory()->addonIntroduction()->publish()->create();
        $this->article2 = Article::factory()->addonPost()->publish()->create();
    }

    public function test(): void
    {
        Article::factory()->page()->publish()->create();
        $time = CarbonImmutable::create(2000, 1, 2, 3, 4, 5);
        $lazyCollection = $this->articleRepository->fetchAggregatedRanking($time);

        $this->assertInstanceOf(LazyCollection::class, $lazyCollection);
        $this->assertCount(2, $lazyCollection, 'アドオン投稿・紹介記事のみ取得できること');
    }

    public function test公開以外のステータス(): void
    {
        $this->article1->update(['status' => ArticleStatus::Draft]);
        $this->article2->update(['status' => ArticleStatus::Draft]);
        $time = CarbonImmutable::create(2000, 1, 2, 3, 4, 5);
        $lazyCollection = $this->articleRepository->fetchAggregatedRanking($time);

        $this->assertInstanceOf(LazyCollection::class, $lazyCollection);
        $this->assertCount(0, $lazyCollection, '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $this->article1->delete();
        $this->article2->delete();
        $time = CarbonImmutable::create(2000, 1, 2, 3, 4, 5);
        $lazyCollection = $this->articleRepository->fetchAggregatedRanking($time);

        $this->assertInstanceOf(LazyCollection::class, $lazyCollection);
        $this->assertCount(0, $lazyCollection, '削除済み記事は取得できないこと');
    }
}
