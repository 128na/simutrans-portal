<?php

declare(strict_types=1);

namespace Tests\Feature\MCP\UserServer;

use App\Enums\ArticleStatus;
use App\Mcp\Servers\SimutransAddonPortalUserServer;
use App\Mcp\Tools\UserMyArticleListTool;
use App\Mcp\Tools\UserMyArticleShowTool;
use App\Models\Article;
use App\Models\User;
use Tests\Feature\TestCase;

class UserMyArticleListToolTest extends TestCase
{
    public function test_list_returns_own_published_and_draft_articles(): void
    {
        $user = User::factory()->create();
        $published = Article::factory()->for($user)->publish()->create(['slug' => 'my-published-slug']);
        $draft = Article::factory()->for($user)->draft()->create(['slug' => 'my-draft-slug']);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyArticleListTool::class, []);

        $response->assertOk()->assertHasNoErrors();
        $response->assertSee('my-published-slug');
        $response->assertSee('my-draft-slug');
        $response->assertSee(ArticleStatus::Draft->value);
    }

    public function test_list_does_not_return_other_users_articles(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Article::factory()->for($other)->publish()->create(['slug' => 'other-user-article-slug']);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyArticleListTool::class, []);

        $response->assertOk()->assertHasNoErrors();
        $response->assertDontSee('other-user-article-slug');
    }

    public function test_show_returns_own_article_detail(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user)->publish()->create(['slug' => 'show-test-slug']);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyArticleShowTool::class, ['article_id' => $article->id]);

        $response->assertOk()->assertHasNoErrors();
        $response->assertSee('show-test-slug');
        $response->assertSee(ArticleStatus::Publish->value);
        $response->assertSee('"categories"');
        $response->assertSee('"tags"');
    }

    public function test_show_returns_draft_article(): void
    {
        $user = User::factory()->create();
        $draft = Article::factory()->for($user)->draft()->create(['slug' => 'draft-show-slug']);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyArticleShowTool::class, ['article_id' => $draft->id]);

        $response->assertOk()->assertHasNoErrors();
        $response->assertSee('draft-show-slug');
        $response->assertSee(ArticleStatus::Draft->value);
    }

    public function test_show_returns_error_for_other_users_article(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $article = Article::factory()->for($other)->publish()->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyArticleShowTool::class, ['article_id' => $article->id]);

        $response->assertHasErrors();
    }
}
