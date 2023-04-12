<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Repositories\ArticleRepository;
use Illuminate\Support\Collection;
use Tests\ArticleTestCase;

final class FindAllForAnalyticsTest extends ArticleTestCase
{
    private ArticleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        $res = $this->repository->findAllForAnalytics($this->user, [$this->article->id, $this->article2->id], fn () => null);

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(1, $res->count(), '自身の記事のみ取得できること');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => 'draft']);
        $res = $this->repository->findAllForAnalytics($this->user, [$this->article->id], fn () => null);

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(1, $res->count(), '非公開記事も取得できること');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $res = $this->repository->findAllForAnalytics($this->user, [$this->article->id], fn () => null);

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(0, $res->count(), '削除済み記事は取得できないこと');
    }
}
