<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\MyListController;

use App\Models\MyList;
use App\Models\MyListItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetItemsTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_list_items_for_authenticated_owner(): void
    {
        // Arrange
        $user = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $user->id]);
        MyListItem::factory()->count(5)->create(['list_id' => $list->id]);

        // Act
        $res = $this->actingAs($user)->getJson("/api/v1/mylist/{$list->id}/items");

        // Assert
        $res->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonStructure([
                'ok',
                'data' => [
                    'items' => [
                        '*' => ['id', 'note', 'position', 'created_at', 'article'],
                    ],
                ],
            ]);

        $this->assertCount(5, $res->json('data.items'));
    }

    public function test_returns_items_ordered_by_position(): void
    {
        $user = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $user->id]);

        $item1 = MyListItem::factory()->create(['list_id' => $list->id, 'position' => 3]);
        $item2 = MyListItem::factory()->create(['list_id' => $list->id, 'position' => 1]);
        $item3 = MyListItem::factory()->create(['list_id' => $list->id, 'position' => 2]);

        $res = $this->actingAs($user)->getJson("/api/v1/mylist/{$list->id}/items");

        $res->assertOk();
        $items = $res->json('data.items');
        $this->assertEquals($item2->id, $items[0]['id']);
        $this->assertEquals($item3->id, $items[1]['id']);
        $this->assertEquals($item1->id, $items[2]['id']);
    }

    public function test_returns_401_when_unauthenticated(): void
    {
        /** @var MyList $list */
        $list = MyList::factory()->create();

        $this->getJson("/api/v1/mylist/{$list->id}/items")->assertUnauthorized();
    }

    public function test_returns_403_when_accessing_other_users_list(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($otherUser)
            ->getJson("/api/v1/mylist/{$list->id}/items")
            ->assertForbidden();
    }

    public function test_returns_404_when_list_not_found(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->getJson('/api/v1/mylist/9999/items')->assertNotFound();
    }
}
