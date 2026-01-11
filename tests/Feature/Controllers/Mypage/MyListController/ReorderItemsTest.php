<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\MyListController;

use App\Models\MyList;
use App\Models\MyListItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReorderItemsTest extends TestCase
{
    use RefreshDatabase;

    public function test_reorders_items_successfully(): void
    {
        // Arrange
        $user = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $user->id]);

        $item1 = MyListItem::factory()->create(['list_id' => $list->id, 'position' => 1]);
        $item2 = MyListItem::factory()->create(['list_id' => $list->id, 'position' => 2]);
        $item3 = MyListItem::factory()->create(['list_id' => $list->id, 'position' => 3]);

        // Act: 順序を逆転
        $res = $this->actingAs($user)->patchJson("/api/v1/mylist/{$list->id}/items/reorder", [
            'items' => [
                ['id' => $item3->id, 'position' => 1],
                ['id' => $item2->id, 'position' => 2],
                ['id' => $item1->id, 'position' => 3],
            ],
        ]);

        // Assert
        $res->assertOk();

        $this->assertDatabaseHas('mylist_items', ['id' => $item3->id, 'position' => 1]);
        $this->assertDatabaseHas('mylist_items', ['id' => $item2->id, 'position' => 2]);
        $this->assertDatabaseHas('mylist_items', ['id' => $item1->id, 'position' => 3]);
    }

    public function test_returns_401_when_unauthenticated(): void
    {
        /** @var MyList $list */
        $list = MyList::factory()->create();

        $this->patchJson("/api/v1/mylist/{$list->id}/items/reorder", ['items' => []])
            ->assertUnauthorized();
    }

    public function test_returns_403_when_accessing_other_users_list(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $owner->id]);
        $item = MyListItem::factory()->create(['list_id' => $list->id]);

        $this->actingAs($otherUser)
            ->patchJson("/api/v1/mylist/{$list->id}/items/reorder", [
                'items' => [
                    ['id' => $item->id, 'position' => 1],
                ],
            ])
            ->assertForbidden();
    }

    public function test_returns_422_when_items_array_is_missing(): void
    {
        $user = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->patchJson("/api/v1/mylist/{$list->id}/items/reorder", [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['items']);
    }

    public function test_returns_422_when_items_have_invalid_structure(): void
    {
        $user = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->patchJson("/api/v1/mylist/{$list->id}/items/reorder", [
                'items' => [
                    ['id' => 'invalid'],  // position missing
                ],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['items.0.position']);
    }

    public function test_returns_404_when_list_not_found(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patchJson('/api/v1/mylist/9999/items/reorder', ['items' => []])
            ->assertNotFound();
    }
}
