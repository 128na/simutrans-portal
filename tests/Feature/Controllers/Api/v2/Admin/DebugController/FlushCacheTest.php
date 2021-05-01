<?php

namespace Tests\Feature\Controllers\Api\v2\Admin\DebugController;

use App\Jobs\Article\JobUpdateRelated;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class FlushCacheTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setup();
        $this->admin = User::factory()->admin()->create();
    }

    public function test()
    {
        Bus::fake();
        $this->actingAs($this->admin);
        $url = route('api.v2.admin.flushCache');
        $res = $this->postJson($url);
        $res->assertOk();
        Bus::assertDispatched(JobUpdateRelated::class);
    }

    public function test未ログイン()
    {
        $url = route('api.v2.admin.flushCache');
        $res = $this->postJson($url);
        $res->assertUnauthorized();
    }

    public function test管理者以外()
    {
        $this->actingAs($this->user);
        $url = route('api.v2.admin.flushCache');
        $res = $this->postJson($url);
        $res->assertUnauthorized();
    }
}
