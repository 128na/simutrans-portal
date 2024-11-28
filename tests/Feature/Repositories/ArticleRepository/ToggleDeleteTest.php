<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Tests\Feature\TestCase;

final class ToggleDeleteTest extends TestCase
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

    public function test_delete(): void
    {
        $this->assertFalse($this->article->trashed());

        $this->articleRepository->toggleDelete($this->article);

        $this->assertTrue($this->article->fresh()->trashed());
    }

    public function test_restore(): void
    {
        $this->article->delete();
        $this->assertTrue($this->article->fresh()->trashed());

        $this->articleRepository->toggleDelete($this->article);

        $this->assertFalse($this->article->fresh()->trashed());
    }
}
