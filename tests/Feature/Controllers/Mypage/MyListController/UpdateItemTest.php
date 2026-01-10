<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\MyListController;

use App\Models\MyList;
use App\Models\MyListItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_updates_item_successfully(): void
    {
        $user = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $user->id]);
        $item = MyListItem::factory()->create(['list_id' => $list->id, 'note' => 'Old note']);

        $res = $this->actingAs($user)->patchJson("/api/v1/mylist/{$list->id}/items/{$item->id}", [
            'note' => 'New note',
        ]);

        $res->assertOk()
            ->assertJsonPath('data.note', 'New note');

        $this->assertDatabaseHas('mylist_items', [
            'id' => $item->id,
            'note' => 'New note',
        ]);
    }

    public function test_returns_401_when_unauthenticated(): void
    {
        /** @var MyList $list */
        $list = MyList::factory()->create();
        $item = MyListItem::factory()->create(['list_id' => $list->id]);

        $this->patchJson("/api/v1/mylist/{$list->id}/items/{$item->id}", ['note' => 'Test'])
            ->assertUnauthorized();
    }

    public function test_returns_403_when_updating_other_users_item(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $owner->id]);
        $item = MyListItem::factory()->create(['list_id' => $list->id]);

        $this->actingAs($otherUser)
            ->patchJson("/api/v1/mylist/{$list->id}/items/{$item->id}", ['note' => 'Hacked'])
            ->assertForbidden();
    }

    public function test_returns_404_when_list_not_found(): void
    {
        $user = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $user->id]);
        $item = MyListItem::factory()->create(['list_id' => $list->id]);

        $this->actingAs($user)
            ->patchJson("/api/v1/mylist/9999/items/{$item->id}", ['note' => 'Test'])
            ->assertNotFound();
    }

    public function test_returns_404_when_item_not_found(): void
    {
        $user = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->patchJson("/api/v1/mylist/{$list->id}/items/9999", ['note' => 'Test'])
            ->assertNotFound();
    }
}
