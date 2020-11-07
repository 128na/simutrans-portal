<?php

namespace Tests\Feature\Api\v2\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function testFetchUsers()
    {
        $url = route('api.v2.admin.users.index');
        $response = $this->getJson($url);
        $response->assertStatus(401);

        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->getJson($url);
        $response->assertStatus(401);

        $user->update(['role' => 'admin']);
        $response = $this->actingAs($user)->getJson($url);
        $response->assertStatus(200);
    }

    public function testDeleteUser()
    {
        $target_user = User::factory()->create();
        $this->assertNull($target_user->deleted_at);
        $url = route('api.v2.admin.users.destroy', $target_user);
        $response = $this->deleteJson($url);
        $response->assertStatus(401);

        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->deleteJson($url);
        $response->assertStatus(401);

        $user->update(['role' => 'admin']);
        $response = $this->actingAs($user)->deleteJson($url);
        $response->assertStatus(200);
        $this->assertFalse(is_null($target_user->fresh()->deleted_at));

        $response = $this->actingAs($user)->deleteJson($url);
        $response->assertStatus(200);
        $this->assertNull($target_user->refresh()->deleted_at);
    }
}
