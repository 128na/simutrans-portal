<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use Tests\Feature\TestCase;

class ArticleStatusControllerTest extends TestCase
{
    public function test_update_requires_auth(): void
    {
        $this->patchJson('/api/v1/articles/1/status', ['status' => 'draft'])->assertUnauthorized();
    }

    public function test_update_changes_status_to_draft(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->publish()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->patchJson("/api/v1/articles/{$article->id}/status", ['status' => ArticleStatus::Draft->value]);

        $response->assertOk();
        $response->assertJsonFragment(['status' => ArticleStatus::Draft->value]);
        $this->assertDatabaseHas('articles', ['id' => $article->id, 'status' => ArticleStatus::Draft->value]);
    }

    public function test_update_sets_published_at_on_first_publish(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->draft()->create(['published_at' => null]);

        $response = $this->actingAs($user, 'sanctum')
            ->patchJson("/api/v1/articles/{$article->id}/status", ['status' => ArticleStatus::Publish->value]);

        $response->assertOk();
        $this->assertNotNull($article->fresh()->published_at);
    }

    public function test_update_does_not_overwrite_published_at_on_republish(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->publish()->create();
        $originalPublishedAt = $article->published_at;

        $this->actingAs($user, 'sanctum')
            ->patchJson("/api/v1/articles/{$article->id}/status", ['status' => ArticleStatus::Draft->value]);

        $this->actingAs($user, 'sanctum')
            ->patchJson("/api/v1/articles/{$article->id}/status", ['status' => ArticleStatus::Publish->value]);

        $this->assertEquals($originalPublishedAt->toDateTimeString(), $article->fresh()->published_at->toDateTimeString());
    }

    public function test_update_returns_404_for_other_users_article(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $article = Article::factory()->for($other)->publish()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->patchJson("/api/v1/articles/{$article->id}/status", ['status' => ArticleStatus::Draft->value]);

        $response->assertNotFound();
    }

    public function test_update_rejects_invalid_status(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->draft()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->patchJson("/api/v1/articles/{$article->id}/status", ['status' => 'invalid']);

        $response->assertUnprocessable();
    }

    public function test_update_allows_trash_status(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->publish()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->patchJson("/api/v1/articles/{$article->id}/status", ['status' => ArticleStatus::Trash->value]);

        $response->assertOk();
        $response->assertJsonFragment(['status' => ArticleStatus::Trash->value]);
    }
}
