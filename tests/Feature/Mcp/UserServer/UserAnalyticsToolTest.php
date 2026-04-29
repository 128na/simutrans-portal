<?php

declare(strict_types=1);

namespace Tests\Feature\MCP\UserServer;

use App\Mcp\Servers\SimutransAddonPortalUserServer;
use App\Mcp\Tools\UserAnalyticsTool;
use App\Models\Article;
use App\Models\User;
use Tests\Feature\TestCase;

class UserAnalyticsToolTest extends TestCase
{
    public function test_returns_published_articles_with_counts(): void
    {
        $user = User::factory()->create();
        Article::factory()->for($user)->publish()->create(['slug' => 'analytics-test']);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserAnalyticsTool::class, []);

        $response->assertOk()->assertHasNoErrors();
        $response->assertSee('analytics-test');
        $response->assertSee('total_view_count');
        $response->assertSee('total_conversion_count');
    }

    public function test_excludes_draft_articles(): void
    {
        $user = User::factory()->create();
        Article::factory()->for($user)->draft()->create(['slug' => 'draft-slug']);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserAnalyticsTool::class, []);

        $response->assertOk()->assertHasNoErrors();
        $response->assertDontSee('draft-slug');
    }

    public function test_excludes_other_users_articles(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Article::factory()->for($other)->publish()->create(['slug' => 'other-slug']);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserAnalyticsTool::class, []);

        $response->assertOk()->assertHasNoErrors();
        $response->assertDontSee('other-slug');
    }
}
