<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Feature\TestCase;

final class FindOrFailWithTrashedTest extends TestCase
{
    private ArticleRepository $articleRepository;

    private Article $article;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);
        $this->article = Article::factory()->create();
    }

    public function test(): void
    {
        $article = $this->articleRepository->findOrFailWithTrashed($this->article->id);

        $this->assertInstanceOf(Article::class, $article);
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => ArticleStatus::Draft]);
        $article = $this->articleRepository->findOrFailWithTrashed($this->article->id);

        $this->assertInstanceOf(Article::class, $article);
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $article = $this->articleRepository->findOrFailWithTrashed($this->article->id);

        $this->assertInstanceOf(Article::class, $article);
    }

    public function test_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->articleRepository->findOrFailWithTrashed(0);
    }
}
