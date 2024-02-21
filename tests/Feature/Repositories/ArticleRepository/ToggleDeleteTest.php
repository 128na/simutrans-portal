<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Tests\TestCase;

class ToggleDeleteTest extends TestCase
{
    private ArticleRepository $articleRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);
    }

    public function testDelete(): void
    {
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'deleted_at' => null,
        ]);

        $this->articleRepository->toggleDelete($article);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
        ]);
        $this->assertDatabaseMissing('articles', [
            'id' => $article->id,
            'deleted_at' => null,
        ]);
    }

    public function testRestore(): void
    {
        $now = now();
        $article = Article::factory()->create(['user_id' => $this->user->id, 'deleted_at' => $now]);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'deleted_at' => $now,
        ]);

        $this->articleRepository->toggleDelete($article);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'deleted_at' => null,
        ]);
    }
}
