<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Support\LazyCollection;
use Tests\Feature\TestCase;

class CursorCheckLinkTest extends TestCase
{
    private ArticleRepository $repository;

    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
        $this->article = Article::factory()->addonIntroduction()->publish()->create([
            'contents' => [
                'link' => '/not_found_url',
                'exclude_link_check' => false,
            ],
        ]);
    }

    public function test(): void
    {
        Article::factory()->page()->create();
        $lazyCollection = $this->repository->cursorCheckLink();

        $this->assertInstanceOf(LazyCollection::class, $lazyCollection);
        $this->assertEquals(1, $lazyCollection->count(), 'アドオン紹介記事のみ取得できること');
    }

    public function testチェック無効(): void
    {
        $contents = $this->article->contents;
        $contents->exclude_link_check = true;
        $this->article->update([
            'contents' => $contents,
        ]);

        $res = $this->repository->cursorCheckLink();

        $this->assertInstanceOf(LazyCollection::class, $res);
        $this->assertEquals(0, $res->count(), 'チェック無効の記事は取得できないこと');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => ArticleStatus::Draft]);
        $lazyCollection = $this->repository->cursorCheckLink();

        $this->assertInstanceOf(LazyCollection::class, $lazyCollection);
        $this->assertEquals(0, $lazyCollection->count(), '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $lazyCollection = $this->repository->cursorCheckLink();

        $this->assertInstanceOf(LazyCollection::class, $lazyCollection);
        $this->assertEquals(0, $lazyCollection->count(), '削除済み記事は取得できないこと');
    }
}
