<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Repositories\ArticleRepository;
use Illuminate\Support\Collection;
use Tests\ArticleTestCase;

class FindAllForAnalyticsTest extends ArticleTestCase
{
    private ArticleRepository $articleRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        $res = $this->articleRepository->findAllForAnalytics($this->user, [$this->article->id, $this->article2->id], static fn (): null => null);

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(1, $res->count(), '自身の記事のみ取得できること');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => 'draft']);
        $res = $this->articleRepository->findAllForAnalytics($this->user, [$this->article->id], static fn (): null => null);

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(1, $res->count(), '非公開記事も取得できること');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $res = $this->articleRepository->findAllForAnalytics($this->user, [$this->article->id], static fn (): null => null);

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(0, $res->count(), '削除済み記事は取得できないこと');
    }
}
