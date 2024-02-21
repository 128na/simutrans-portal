<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Models\Category;
use App\Repositories\ArticleRepository;
use Tests\TestCase;

class SyncCategoriesTest extends TestCase
{
    private ArticleRepository $articleRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        $category = Category::first();

        $this->assertDatabaseMissing('article_category', [
            'article_id' => $article->id,
            'category_id' => $category->id,
        ]);

        $this->articleRepository->syncCategories($article, [$category->id]);

        $this->assertDatabaseHas('article_category', [
            'article_id' => $article->id,
            'category_id' => $category->id,
        ]);
    }
}
