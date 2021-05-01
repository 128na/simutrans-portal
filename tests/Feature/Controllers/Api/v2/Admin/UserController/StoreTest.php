<?php

namespace Tests\Feature\Controllers\Api\v2\Admin\UserController;

use App\Models\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setup();
        $this->admin = User::factory()->admin()->create();
    }

    public function test()
    {
        $this->actingAs($this->admin);
        $url = route('api.v2.admin.users.store');
        $data = [
            'name' => 'test',
            'email' => 'test@example.com',
        ];
        $this->assertDatabaseMissing('users', $data);
        $res = $this->postJson($url, $data);
        $res->assertCreated();
        $res->assertJsonFragment(['name' => 'test']);

        $this->assertDatabaseHas('users', [
            'name' => 'test',
            'email' => 'test@example.com',
            'role' => 'user',
        ]);
    }

    public function test未ログイン()
    {
        $url = route('api.v2.admin.users.store');
        $res = $this->postJson($url);
        $res->assertUnauthorized();
    }

    public function test管理者以外()
    {
        $this->actingAs($this->user);
        $url = route('api.v2.admin.users.store');
        $res = $this->postJson($url);
        $res->assertUnauthorized();
    }
}
