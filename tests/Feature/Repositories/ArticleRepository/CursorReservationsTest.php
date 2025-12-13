<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\LazyCollection;
use Tests\Feature\TestCase;

class CursorReservationsTest extends TestCase
{
    private ArticleRepository $articleRepository;

    private Article $article;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);
        $this->article = Article::factory()->create([
            'status' => ArticleStatus::Reservation,
            'published_at' => CarbonImmutable::create(2000, 1, 2, 3, 4, 5),
        ]);
    }

    public function test(): void
    {
        $time = CarbonImmutable::create(2000, 1, 2, 3, 4, 5);
        $lazyCollection = $this->articleRepository->cursorReservations($time);

        $this->assertInstanceOf(LazyCollection::class, $lazyCollection);
        $this->assertCount(1, $lazyCollection, '公開時刻を過ぎた予約投稿記事のみ取得出来ること');
    }

    public function test公開時刻前(): void
    {
        $time = CarbonImmutable::create(2000, 1, 2, 3, 4, 4);
        $lazyCollection = $this->articleRepository->cursorReservations($time);

        $this->assertInstanceOf(LazyCollection::class, $lazyCollection);
        $this->assertEmpty($lazyCollection, '公開時刻前の予約投稿記事は取得できないこと');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => ArticleStatus::Draft]);
        $time = CarbonImmutable::create(2000, 1, 2, 3, 4, 5);
        $lazyCollection = $this->articleRepository->cursorReservations($time);

        $this->assertInstanceOf(LazyCollection::class, $lazyCollection);
        $this->assertEmpty($lazyCollection, '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $time = CarbonImmutable::create(2000, 1, 2, 3, 4, 5);
        $lazyCollection = $this->articleRepository->cursorReservations($time);

        $this->assertInstanceOf(LazyCollection::class, $lazyCollection);
        $this->assertEmpty($lazyCollection, '削除済み記事は取得できないこと');
    }
}
