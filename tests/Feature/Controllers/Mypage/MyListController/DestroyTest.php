<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\MyListController;

use App\Models\MyList;
use App\Models\MyListItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_deletes_list_successfully(): void
    {
        $user = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $user->id]);

        $res = $this->actingAs($user)->deleteJson("/api/v1/mylist/{$list->id}");

        $res->assertOk();

        $this->assertDatabaseMissing('mylists', ['id' => $list->id]);
    }

    public function test_deletes_list_and_cascades_items(): void
    {
        $user = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $user->id]);
        $item = MyListItem::factory()->create(['list_id' => $list->id]);

        $this->actingAs($user)->deleteJson("/api/v1/mylist/{$list->id}")->assertOk();

        $this->assertDatabaseMissing('mylists', ['id' => $list->id]);
        $this->assertDatabaseMissing('mylist_items', ['id' => $item->id]);
    }

    public function test_returns_401_when_unauthenticated(): void
    {
        /** @var MyList $list */
        $list = MyList::factory()->create();

        $this->deleteJson("/api/v1/mylist/{$list->id}")->assertUnauthorized();
    }

    public function test_returns_403_when_deleting_other_users_list(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($otherUser)
            ->deleteJson("/api/v1/mylist/{$list->id}")
            ->assertForbidden();
    }

    public function test_returns_404_when_list_not_found(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->deleteJson('/api/v1/mylist/9999')->assertNotFound();
    }
}
