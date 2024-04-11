<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Tests\Feature\TestCase;

final class SyncCategoriesTest extends TestCase
{
    private ArticleRepository $articleRepository;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->articleRepository = app(ArticleRepository::class);
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

        $this->articleRepository->syncCategories($article, [$shouldAddCategory->id]);

        $this->assertSame(
            [$shouldAddCategory->id],
            $article->categories()->pluck('id')->toArray()
        );
    }
}
