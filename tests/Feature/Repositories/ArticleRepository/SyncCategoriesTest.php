<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Models\Category;
use App\Repositories\ArticleRepository;
use Tests\TestCase;

class SyncCategoriesTest extends TestCase
{
    private ArticleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
    }

    public function test()
    {
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        $category = Category::first();

        $this->assertDatabaseMissing('article_category', [
            'article_id' => $article->id,
            'category_id' => $category->id,
        ]);

        $this->repository->syncCategories($article, [$category->id]);

        $this->assertDatabaseHas('article_category', [
            'article_id' => $article->id,
            'category_id' => $category->id,
        ]);
    }
}
