<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\MyListController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_list_successfully(): void
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->postJson('/api/v1/mylist', [
            'title' => 'Test List',
            'note' => 'Test note',
            'is_public' => true,
        ]);

        $res->assertCreated()
            ->assertJsonPath('data.title', 'Test List')
            ->assertJsonPath('data.is_public', true);

        $this->assertDatabaseHas('mylists', [
            'user_id' => $user->id,
            'title' => 'Test List',
            'is_public' => true,
        ]);
    }

    public function test_creates_list_with_minimum_fields(): void
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->postJson('/api/v1/mylist', [
            'title' => 'Minimal List',
        ]);

        $res->assertCreated()
            ->assertJsonPath('data.title', 'Minimal List')
            ->assertJsonPath('data.is_public', false);
    }

    public function test_generates_slug_when_list_is_public(): void
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->postJson('/api/v1/mylist', [
            'title' => 'Public List',
            'is_public' => true,
        ]);

        $res->assertCreated();
        $slug = $res->json('data.slug');
        $this->assertNotNull($slug);
        $this->assertIsString($slug);
    }

    public function test_returns_401_when_unauthenticated(): void
    {
        $this->postJson('/api/v1/mylist', ['title' => 'Test'])->assertUnauthorized();
    }

    public function test_returns_422_when_title_is_missing(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/v1/mylist', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    public function test_returns_422_when_title_exceeds_max_length(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/v1/mylist', ['title' => str_repeat('a', 121)])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }
}
