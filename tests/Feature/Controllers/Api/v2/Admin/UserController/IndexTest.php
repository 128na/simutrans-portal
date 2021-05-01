<?php

namespace Tests\Feature\Controllers\Api\v2\Admin\UserController;

use App\Models\User;
use Tests\TestCase;

class IndexTest extends TestCase
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
        $url = route('api.v2.admin.users.index');
        $res = $this->getJson($url);
        $res->assertOk();
        $res->assertJsonFragment(['name' => $this->admin->name]);
        $res->assertJsonFragment(['name' => $this->user->name]);
    }

    public function test未ログイン()
    {
        $url = route('api.v2.admin.users.index');
        $res = $this->getJson($url);
        $res->assertUnauthorized();
    }

    public function test管理者以外()
    {
        $this->actingAs($this->user);
        $url = route('api.v2.admin.users.index');
        $res = $this->getJson($url);
        $res->assertUnauthorized();
    }
}
