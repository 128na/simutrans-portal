<?php

namespace Tests\Feature\Api\v2\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiscTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function testFlushCache()
    {
        $url = route('api.v2.admin.flushCache');
        $response = $this->postJson($url);
        $response->assertStatus(401);

        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->postJson($url);
        $response->assertStatus(401);

        $user->update(['role' => 'admin']);
        $response = $this->actingAs($user)->postJson($url);
        $response->assertStatus(200);
    }

    public function testPhpinfo()
    {
        $url = route('api.v2.admin.phpinfo');
        $response = $this->getJson($url);
        $response->assertStatus(401);

        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->getJson($url);
        $response->assertStatus(401);

        $user->update(['role' => 'admin']);
        $response = $this->actingAs($user)->getJson($url);
        $response->assertStatus(200);
    }

    public function testError()
    {
        $url = route('api.v2.admin.debug', ['level' => 'error']);
        $response = $this->getJson($url);
        $response->assertStatus(401);

        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->getJson($url);
        $response->assertStatus(401);

        $user->update(['role' => 'admin']);
        $response = $this->actingAs($user)->getJson($url);
        $response->assertStatus(500);
    }

    public function testWarnig()
    {
        $url = route('api.v2.admin.debug', ['level' => 'warning']);
        $response = $this->getJson($url);
        $response->assertStatus(401);

        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->getJson($url);
        $response->assertStatus(401);

        $user->update(['role' => 'admin']);
        $response = $this->actingAs($user)->getJson($url);
        $response->assertStatus(500);
    }

    public function testNotice()
    {
        $url = route('api.v2.admin.debug', ['level' => 'notice']);
        $response = $this->getJson($url);
        $response->assertStatus(401);

        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->getJson($url);
        $response->assertStatus(401);

        $user->update(['role' => 'admin']);
        $response = $this->actingAs($user)->getJson($url);
        $response->assertStatus(500);
    }
}
