<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Tests\Feature\TestCase;

final class FindRandomPRTest extends TestCase
{
    private ArticleRepository $articleRepository;

    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);
        $this->article = Article::factory()->publish()->create(['pr' => true]);
    }

    public function test(): void
    {
        $res = $this->articleRepository->findRandomPR();

        $this->assertInstanceOf(Article::class, $res);
    }

    public function testPR以外(): void
    {
        $this->article->update(['pr' => false]);
        $res = $this->articleRepository->findRandomPR();

        $this->assertNull($res);
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => ArticleStatus::Draft]);
        $res = $this->articleRepository->findRandomPR();

        $this->assertNull($res);
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $res = $this->articleRepository->findRandomPR();

        $this->assertNull($res);
    }
}
