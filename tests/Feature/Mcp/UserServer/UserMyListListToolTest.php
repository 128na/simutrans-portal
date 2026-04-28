<?php

declare(strict_types=1);

namespace Tests\Feature\MCP\UserServer;

use App\Mcp\Servers\SimutransAddonPortalUserServer;
use App\Mcp\Tools\UserMyListListTool;
use App\Mcp\Tools\UserMyListShowTool;
use App\Models\Article;
use App\Models\MyList;
use App\Models\User;
use Tests\Feature\TestCase;

class UserMyListListToolTest extends TestCase
{
    public function test_list_returns_own_lists_including_private(): void
    {
        $user = User::factory()->create();
        MyList::factory()->for($user)->public()->create(['title' => 'Public My List Title']);
        MyList::factory()->for($user)->create(['title' => 'Private My List Title']);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyListListTool::class, []);

        $response->assertOk()->assertHasNoErrors();
        $response->assertSee('Public My List Title');
        $response->assertSee('Private My List Title');
        $response->assertSee('"is_public"');
        $response->assertSee('"items_count"');
    }

    public function test_list_does_not_return_other_users_lists(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        MyList::factory()->for($other)->public()->create(['title' => 'Other User Public List']);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyListListTool::class, []);

        $response->assertOk()->assertHasNoErrors();
        $response->assertDontSee('Other User Public List');
    }

    public function test_show_returns_private_mylist_with_items(): void
    {
        $user = User::factory()->create();
        $mylist = MyList::factory()->for($user)->create(['title' => 'My Private List With Items']);
        $article = Article::factory()->for($user)->publish()->create(['slug' => 'mylist-article-slug']);
        $mylist->items()->create(['article_id' => $article->id, 'position' => 1, 'note' => 'item note here']);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyListShowTool::class, ['mylist_id' => $mylist->id]);

        $response->assertOk()->assertHasNoErrors();
        $response->assertSee('My Private List With Items');
        $response->assertSee('mylist-article-slug');
        $response->assertSee('item note here');
        $response->assertSee('"list"');
        $response->assertSee('"data"');
    }

    public function test_show_returns_error_for_other_users_list(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $mylist = MyList::factory()->for($other)->public()->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyListShowTool::class, ['mylist_id' => $mylist->id]);

        $response->assertHasErrors();
    }
}
