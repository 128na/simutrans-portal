<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\MyListController;

use App\Models\MyList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_user_lists_successfully(): void
    {
        $user = User::factory()->create();
        MyList::factory()->count(3)->create(['user_id' => $user->id]);
        MyList::factory()->count(2)->create(); // 他ユーザーのリスト

        $res = $this->actingAs($user)->getJson('/api/v1/mylist');

        $res->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'note', 'is_public', 'slug', 'items_count', 'created_at', 'updated_at'],
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'from', 'path', 'per_page', 'to'],
            ]);

        $this->assertCount(3, $res->json('data'));
    }

    public function test_returns_empty_array_when_no_lists(): void
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->getJson('/api/v1/mylist');

        $res->assertOk()
            ->assertJsonPath('data', []);
    }

    public function test_returns_401_when_unauthenticated(): void
    {
        $this->getJson('/api/v1/mylist')->assertUnauthorized();
    }
}
