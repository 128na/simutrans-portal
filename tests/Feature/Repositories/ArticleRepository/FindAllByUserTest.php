<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Support\Collection;
use Tests\Feature\TestCase;

final class FindAllByUserTest extends TestCase
{
    private Article $article;

    private ArticleRepository $articleRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->article = Article::factory()->create();
        $this->articleRepository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        Article::factory()->create();

        $res = $this->articleRepository->findAllByUser($this->article->user);
        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(1, $res->count(), '自身の記事のみ取得できること');
        $this->assertSame($this->article->title, $res->first()->title);
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => ArticleStatus::Draft]);
        $res = $this->articleRepository->findAllByUser($this->article->user);

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(1, $res->count(), '非公開記事も取得できること');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $res = $this->articleRepository->findAllByUser($this->article->user);

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEquals(0, $res->count(), '削除済み記事は取得できないこと');
    }
}
