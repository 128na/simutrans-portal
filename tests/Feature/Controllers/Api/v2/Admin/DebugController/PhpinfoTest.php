<?php

namespace Tests\Feature\Controllers\Api\v2\Admin\DebugController;

use App\Models\User;
use Tests\TestCase;

class PhpinfoTest extends TestCase
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
        $url = route('api.v2.admin.phpinfo');
        $res = $this->getJson($url);
        $res->assertOk();
    }

    public function test未ログイン()
    {
        $url = route('api.v2.admin.phpinfo');
        $res = $this->getJson($url);
        $res->assertUnauthorized();
    }

    public function test管理者以外()
    {
        $this->actingAs($this->user);
        $url = route('api.v2.admin.phpinfo');
        $res = $this->getJson($url);
        $res->assertUnauthorized();
    }
}
