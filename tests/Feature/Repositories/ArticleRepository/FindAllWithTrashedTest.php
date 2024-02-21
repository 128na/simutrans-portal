<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Repositories\ArticleRepository;
use Illuminate\Support\Collection;
use Tests\ArticleTestCase;

class FindAllWithTrashedTest extends ArticleTestCase
{
    private ArticleRepository $articleRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        $res = $this->articleRepository->findAllWithTrashed();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(2, $res->count(), '全ての記事が取得できること');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => 'draft']);
        $res = $this->articleRepository->findAllWithTrashed();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(2, $res->count(), '非公開記事も取得できること');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $res = $this->articleRepository->findAllWithTrashed();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(2, $res->count(), '削除済み記事も取得できること');
    }
}
