<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Tests\Feature\TestCase;

final class SyncArticlesTest extends TestCase
{
    private ArticleRepository $articleRepository;

    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->articleRepository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        $article = Article::factory()->create(['user_id' => $this->user->id]);
        $shouldAddArticle = Article::factory()->create();
        $shouldRemoveArticle = Article::factory()->create();
        $article->articles()->save($shouldRemoveArticle);

        $this->assertSame([$shouldRemoveArticle->id], $article->articles()->pluck('id')->toArray());
        $this->articleRepository->syncArticles($article, [$shouldAddArticle->id]);
        $this->assertSame([$shouldAddArticle->id], $article->articles()->pluck('id')->toArray());
    }
}
