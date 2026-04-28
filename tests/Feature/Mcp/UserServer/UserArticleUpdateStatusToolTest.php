<?php

declare(strict_types=1);

namespace Tests\Feature\MCP\UserServer;

use App\Enums\ArticleStatus;
use App\Mcp\Servers\SimutransAddonPortalUserServer;
use App\Mcp\Tools\UserArticleUpdateStatusTool;
use App\Models\Article;
use App\Models\User;
use Tests\Feature\TestCase;

class UserArticleUpdateStatusToolTest extends TestCase
{
    public function test_publish_draft_article(): void
    {
        $user = User::factory()->create();
        $draft = Article::factory()->for($user)->draft()->create(['slug' => 'status-test-slug']);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleUpdateStatusTool::class, [
                'article_id' => $draft->id,
                'status' => ArticleStatus::Publish->value,
            ]);

        $response->assertOk()->assertHasNoErrors();
        $response->assertSee(ArticleStatus::Publish->value);
        $response->assertSee('status-test-slug');

        $draft->refresh();
        $this->assertSame(ArticleStatus::Publish, $draft->status);
        $this->assertNotNull($draft->published_at);
    }

    public function test_unpublish_to_draft(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->publish()->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleUpdateStatusTool::class, [
                'article_id' => $article->id,
                'status' => ArticleStatus::Draft->value,
            ]);

        $response->assertOk()->assertHasNoErrors();
        $response->assertSee(ArticleStatus::Draft->value);

        $article->refresh();
        $this->assertSame(ArticleStatus::Draft, $article->status);
    }

    public function test_change_to_private(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->publish()->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleUpdateStatusTool::class, [
                'article_id' => $article->id,
                'status' => ArticleStatus::Private->value,
            ]);

        $response->assertOk()->assertHasNoErrors();
        $article->refresh();
        $this->assertSame(ArticleStatus::Private, $article->status);
    }

    public function test_invalid_status_returns_error(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->publish()->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleUpdateStatusTool::class, [
                'article_id' => $article->id,
                'status' => 'invalid-status',
            ]);

        $response->assertHasErrors();
        $article->refresh();
        $this->assertSame(ArticleStatus::Publish, $article->status);
    }

    public function test_returns_error_for_other_users_article(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $article = Article::factory()->for($other)->publish()->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleUpdateStatusTool::class, [
                'article_id' => $article->id,
                'status' => ArticleStatus::Draft->value,
            ]);

        $response->assertHasErrors();
        $article->refresh();
        $this->assertSame(ArticleStatus::Publish, $article->status);
    }

    public function test_first_publish_sets_published_at(): void
    {
        $user = User::factory()->create();
        $draft = Article::factory()->for($user)->draft()->create(['published_at' => null]);

        $this->assertNull($draft->published_at);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleUpdateStatusTool::class, [
                'article_id' => $draft->id,
                'status' => ArticleStatus::Publish->value,
            ]);

        $response->assertOk()->assertHasNoErrors();
        $draft->refresh();
        $this->assertNotNull($draft->published_at);
    }

    public function test_republish_does_not_reset_published_at(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->publish()->create();
        $originalPublishedAt = $article->published_at;

        $this->assertNotNull($originalPublishedAt);

        SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleUpdateStatusTool::class, [
                'article_id' => $article->id,
                'status' => ArticleStatus::Draft->value,
            ]);

        SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleUpdateStatusTool::class, [
                'article_id' => $article->id,
                'status' => ArticleStatus::Publish->value,
            ]);

        $article->refresh();
        $this->assertEquals($originalPublishedAt, $article->published_at);
    }
}
