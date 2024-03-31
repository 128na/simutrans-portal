<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Tests\Feature\TestCase;

class SyncCategoriesTest extends TestCase
{
    private ArticleRepository $repository;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->repository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        $article = Article::factory()->create(['user_id' => $this->user->id]);

        $shouldAddCategory = Category::factory()->create();
        $shouldRemoveCategory = Category::factory()->create();
        $article->categories()->save($shouldRemoveCategory);

        $this->assertSame(
            [$shouldRemoveCategory->id],
            $article->categories()->pluck('id')->toArray()
        );

        $this->repository->syncCategories($article, [$shouldAddCategory->id]);

        $this->assertSame(
            [$shouldAddCategory->id],
            $article->categories()->pluck('id')->toArray()
        );
    }
}
