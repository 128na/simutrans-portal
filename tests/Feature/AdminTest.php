<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function testNeedLogin()
    {
        $url = route('admin.index');
        $response = $this->get($url);
        $response->assertStatus(302);
        $response->assertRedirect(route('mypage.index'));

        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);
        $response = $this->get($url);
        $response->assertStatus(404);

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $response = $this->get($url);
        $response->assertOk();
    }
}
