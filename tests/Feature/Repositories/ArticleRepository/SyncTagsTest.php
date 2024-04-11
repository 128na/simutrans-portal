<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Tests\Feature\TestCase as FeatureTestCase;

final class SyncTagsTest extends FeatureTestCase
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
        $shouldAddTag = Tag::factory()->create();
        $shouldRemoveTag = Tag::factory()->create();
        $article->tags()->save($shouldRemoveTag);

        $this->assertSame(
            [$shouldRemoveTag->id],
            $article->tags()->pluck('id')->toArray()
        );

        $this->articleRepository->syncTags($article, [$shouldAddTag->id]);

        $this->assertSame(
            [$shouldAddTag->id],
            $article->tags()->pluck('id')->toArray()
        );
    }
}
