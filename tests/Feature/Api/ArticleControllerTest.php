<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use Tests\Feature\TestCase;

class ArticleControllerTest extends TestCase
{
    public function test_index_requires_auth(): void
    {
        $this->getJson('/api/v1/articles')->assertUnauthorized();
    }

    public function test_index_returns_user_articles(): void
    {
        $user = User::factory()->create();
        Article::factory()->for($user)->draft()->create(['slug' => 'my-draft']);
        Article::factory()->for($user)->publish()->create(['slug' => 'my-publish']);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/articles');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['slug' => 'my-draft']);
        $response->assertJsonFragment(['slug' => 'my-publish']);
        $response->assertJsonStructure(['data' => [['id', 'title', 'slug', 'status', 'post_type', 'published_at', 'modified_at']]]);
    }

    public function test_index_excludes_other_users_articles(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Article::factory()->for($other)->publish()->create(['slug' => 'other-slug']);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/articles');

        $response->assertOk();
        $response->assertJsonMissing(['slug' => 'other-slug']);
    }

    public function test_show_requires_auth(): void
    {
        $this->getJson('/api/v1/articles/1')->assertUnauthorized();
    }

    public function test_show_returns_article_detail(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->draft()->create(['slug' => 'detail-slug']);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/v1/articles/{$article->id}");

        $response->assertOk();
        $response->assertJsonFragment(['slug' => 'detail-slug']);
        $response->assertJsonStructure(['id', 'title', 'slug', 'status', 'post_type', 'categories', 'tags']);
    }

    public function test_show_returns_404_for_other_users_article(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $article = Article::factory()->for($other)->publish()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/v1/articles/{$article->id}");

        $response->assertNotFound();
    }

    public function test_show_returns_404_for_nonexistent_article(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/articles/99999');

        $response->assertNotFound();
    }

    public function test_show_includes_draft_articles(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->draft()->create(['slug' => 'draft-detail']);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/v1/articles/{$article->id}");

        $response->assertOk();
        $response->assertJsonFragment(['status' => ArticleStatus::Draft->value]);
    }
}
