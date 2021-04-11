<?php

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Models\Tag;
use App\Repositories\ArticleRepository;
use Tests\TestCase;

class SyncTagsTest extends TestCase
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

        $tag = Tag::factory()->create();

        $this->assertDatabaseMissing('article_tag', [
            'article_id' => $article->id,
            'tag_id' => $tag->id,
        ]);

        $this->repository->syncTags($article, [$tag->id]);

        $this->assertDatabaseHas('article_tag', [
            'article_id' => $article->id,
            'tag_id' => $tag->id,
        ]);
    }
}
