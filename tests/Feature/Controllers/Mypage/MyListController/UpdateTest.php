<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\MyListController;

use App\Models\MyList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_updates_list_successfully(): void
    {
        $user = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $user->id, 'title' => 'Old Title']);

        $res = $this->actingAs($user)->patchJson("/api/v1/mylist/{$list->id}", [
            'title' => 'New Title',
            'note' => 'Updated note',
            'is_public' => true,
        ]);

        $res->assertOk()
            ->assertJsonPath('data.title', 'New Title');

        $this->assertDatabaseHas('mylists', [
            'id' => $list->id,
            'title' => 'New Title',
            'note' => 'Updated note',
            'is_public' => true,
        ]);
    }

    public function test_generates_slug_when_changing_to_public(): void
    {
        // リストは作成時に常に slug を生成するため、このテストはスキップ
        $this->markTestSkipped('Slug is generated at list creation, not at update');
    }

    public function test_returns_401_when_unauthenticated(): void
    {
        /** @var MyList $list */
        $list = MyList::factory()->create();

        $this->patchJson("/api/v1/mylist/{$list->id}", ['title' => 'Test'])
            ->assertUnauthorized();
    }

    public function test_returns_403_when_updating_other_users_list(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($otherUser)
            ->patchJson("/api/v1/mylist/{$list->id}", ['title' => 'Hacked'])
            ->assertForbidden();
    }

    public function test_returns_404_when_list_not_found(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patchJson('/api/v1/mylist/9999', ['title' => 'Test'])
            ->assertNotFound();
    }

    public function test_returns_422_when_title_is_invalid(): void
    {
        $user = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->patchJson("/api/v1/mylist/{$list->id}", ['title' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }
}
