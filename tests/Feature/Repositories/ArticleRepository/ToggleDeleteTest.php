<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Tests\Feature\TestCase;

final class ToggleDeleteTest extends TestCase
{
    private ArticleRepository $repository;

    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
        $this->article = Article::factory()->create();
    }

    public function testDelete(): void
    {
        $this->assertFalse($this->article->trashed());

        $this->repository->toggleDelete($this->article);

        $this->assertTrue($this->article->fresh()->trashed());
    }

    public function testRestore(): void
    {
        $this->article->delete();
        $this->assertTrue($this->article->fresh()->trashed());

        $this->repository->toggleDelete($this->article);

        $this->assertFalse($this->article->fresh()->trashed());
    }
}
