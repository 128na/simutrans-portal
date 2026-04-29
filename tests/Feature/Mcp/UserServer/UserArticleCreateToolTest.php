<?php

declare(strict_types=1);

namespace Tests\Feature\MCP\UserServer;

use App\Enums\ArticleStatus;
use App\Mcp\Servers\SimutransAddonPortalUserServer;
use App\Mcp\Tools\UserArticleCreateTool;
use App\Models\User;
use Tests\Feature\TestCase;

class UserArticleCreateToolTest extends TestCase
{
    public function test_creates_draft_markdown_article(): void
    {
        $user = User::factory()->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreateTool::class, [
                'title' => 'My Test Article',
                'slug' => 'my-test-article',
                'markdown' => '# Hello\nThis is a test.',
                'status' => ArticleStatus::Draft->value,
            ]);

        $response->assertOk()->assertHasNoErrors();
        $response->assertSee('my-test-article');
        $response->assertSee(ArticleStatus::Draft->value);
        $response->assertSee('markdown');

        $this->assertDatabaseHas('articles', [
            'user_id' => $user->id,
            'slug' => 'my-test-article',
            'title' => 'My Test Article',
        ]);
    }

    public function test_creates_published_article_sets_published_at(): void
    {
        $user = User::factory()->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreateTool::class, [
                'title' => 'Published Article',
                'slug' => 'published-article',
                'markdown' => '# Published',
                'status' => ArticleStatus::Publish->value,
            ]);

        $response->assertOk()->assertHasNoErrors();
        $response->assertSee(ArticleStatus::Publish->value);
        $response->assertSee('published_at');
    }

    public function test_duplicate_slug_returns_error(): void
    {
        $user = User::factory()->create();

        SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreateTool::class, [
                'title' => 'First',
                'slug' => 'same-slug',
                'markdown' => '# First',
                'status' => ArticleStatus::Draft->value,
            ]);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreateTool::class, [
                'title' => 'Second',
                'slug' => 'same-slug',
                'markdown' => '# Second',
                'status' => ArticleStatus::Draft->value,
            ]);

        $response->assertHasErrors();
    }

    public function test_invalid_status_returns_error(): void
    {
        $user = User::factory()->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreateTool::class, [
                'title' => 'Test',
                'slug' => 'test-slug',
                'markdown' => '# Test',
                'status' => 'trash',
            ]);

        $response->assertHasErrors();
        $this->assertDatabaseMissing('articles', ['slug' => 'test-slug', 'user_id' => $user->id]);
    }

    public function test_invalid_slug_characters_return_error(): void
    {
        $user = User::factory()->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreateTool::class, [
                'title' => 'Test',
                'slug' => 'スラッグ',
                'markdown' => '# Test',
                'status' => ArticleStatus::Draft->value,
            ]);

        $response->assertHasErrors();
    }

    public function test_same_slug_allowed_for_different_users(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        SimutransAddonPortalUserServer::actingAs($other)
            ->tool(UserArticleCreateTool::class, [
                'title' => 'Other User Article',
                'slug' => 'shared-slug',
                'markdown' => '# Other',
                'status' => ArticleStatus::Draft->value,
            ]);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserArticleCreateTool::class, [
                'title' => 'My Article',
                'slug' => 'shared-slug',
                'markdown' => '# Mine',
                'status' => ArticleStatus::Draft->value,
            ]);

        $response->assertOk()->assertHasNoErrors();
    }
}
