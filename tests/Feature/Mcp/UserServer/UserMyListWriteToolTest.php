<?php

declare(strict_types=1);

namespace Tests\Feature\MCP\UserServer;

use App\Mcp\Servers\SimutransAddonPortalUserServer;
use App\Mcp\Tools\UserMyListAddItemTool;
use App\Mcp\Tools\UserMyListCreateTool;
use App\Mcp\Tools\UserMyListDeleteTool;
use App\Mcp\Tools\UserMyListRemoveItemTool;
use App\Mcp\Tools\UserMyListUpdateTool;
use App\Models\Article;
use App\Models\MyList;
use App\Models\User;
use Tests\Feature\TestCase;

class UserMyListWriteToolTest extends TestCase
{
    public function test_create_makes_new_mylist(): void
    {
        $user = User::factory()->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyListCreateTool::class, [
                'title' => 'New Test List',
                'note' => 'A note here',
                'is_public' => false,
            ]);

        $response->assertOk()->assertHasNoErrors();
        $response->assertSee('New Test List');
        $response->assertSee('A note here');
        $this->assertDatabaseHas('mylists', ['user_id' => $user->id, 'title' => 'New Test List']);
    }

    public function test_create_with_is_public_true(): void
    {
        $user = User::factory()->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyListCreateTool::class, [
                'title' => 'Public List',
                'is_public' => true,
            ]);

        $response->assertOk()->assertHasNoErrors();
        $this->assertDatabaseHas('mylists', ['user_id' => $user->id, 'is_public' => true]);
    }

    public function test_update_changes_mylist(): void
    {
        $user = User::factory()->create();
        $mylist = MyList::factory()->for($user)->create(['title' => 'Old Title']);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyListUpdateTool::class, [
                'mylist_id' => $mylist->id,
                'title' => 'Updated Title',
                'is_public' => true,
            ]);

        $response->assertOk()->assertHasNoErrors();
        $response->assertSee('Updated Title');
        $this->assertDatabaseHas('mylists', ['id' => $mylist->id, 'title' => 'Updated Title', 'is_public' => true]);
    }

    public function test_update_returns_error_for_other_users_list(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $mylist = MyList::factory()->for($other)->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyListUpdateTool::class, [
                'mylist_id' => $mylist->id,
                'title' => 'Hijacked Title',
            ]);

        $response->assertHasErrors();
        $this->assertDatabaseMissing('mylists', ['id' => $mylist->id, 'title' => 'Hijacked Title']);
    }

    public function test_delete_removes_mylist(): void
    {
        $user = User::factory()->create();
        $mylist = MyList::factory()->for($user)->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyListDeleteTool::class, ['mylist_id' => $mylist->id]);

        $response->assertOk()->assertHasNoErrors();
        $this->assertDatabaseMissing('mylists', ['id' => $mylist->id]);
    }

    public function test_delete_returns_error_for_other_users_list(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $mylist = MyList::factory()->for($other)->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyListDeleteTool::class, ['mylist_id' => $mylist->id]);

        $response->assertHasErrors();
        $this->assertDatabaseHas('mylists', ['id' => $mylist->id]);
    }

    public function test_add_item_appends_published_article_to_mylist(): void
    {
        $user = User::factory()->create();
        $mylist = MyList::factory()->for($user)->create();
        $article = Article::factory()->for($user)->publish()->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyListAddItemTool::class, [
                'mylist_id' => $mylist->id,
                'article_id' => $article->id,
                'note' => 'My note',
            ]);

        $response->assertOk()->assertHasNoErrors();
        $response->assertSee('My note');
        $this->assertDatabaseHas('mylist_items', ['list_id' => $mylist->id, 'article_id' => $article->id]);
    }

    public function test_add_item_returns_error_for_draft_article(): void
    {
        $user = User::factory()->create();
        $mylist = MyList::factory()->for($user)->create();
        $draft = Article::factory()->for($user)->draft()->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyListAddItemTool::class, [
                'mylist_id' => $mylist->id,
                'article_id' => $draft->id,
            ]);

        $response->assertHasErrors();
        $this->assertDatabaseMissing('mylist_items', ['list_id' => $mylist->id, 'article_id' => $draft->id]);
    }

    public function test_add_item_returns_error_for_other_users_list(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $mylist = MyList::factory()->for($other)->create();
        $article = Article::factory()->for($user)->publish()->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyListAddItemTool::class, [
                'mylist_id' => $mylist->id,
                'article_id' => $article->id,
            ]);

        $response->assertHasErrors();
    }

    public function test_remove_item_deletes_item_from_mylist(): void
    {
        $user = User::factory()->create();
        $mylist = MyList::factory()->for($user)->create();
        $article = Article::factory()->for($user)->publish()->create();
        $item = $mylist->items()->create(['article_id' => $article->id, 'position' => 1]);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyListRemoveItemTool::class, [
                'mylist_id' => $mylist->id,
                'item_id' => $item->id,
            ]);

        $response->assertOk()->assertHasNoErrors();
        $this->assertDatabaseMissing('mylist_items', ['id' => $item->id]);
    }

    public function test_remove_item_returns_error_for_other_users_list(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $mylist = MyList::factory()->for($other)->create();
        $article = Article::factory()->for($other)->publish()->create();
        $item = $mylist->items()->create(['article_id' => $article->id, 'position' => 1]);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserMyListRemoveItemTool::class, [
                'mylist_id' => $mylist->id,
                'item_id' => $item->id,
            ]);

        $response->assertHasErrors();
        $this->assertDatabaseHas('mylist_items', ['id' => $item->id]);
    }
}
