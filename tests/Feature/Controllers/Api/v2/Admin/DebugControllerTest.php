<?php

namespace Tests\Feature\Controllers\Api\v2\Admin;

use App\Jobs\Article\JobUpdateRelated;
use Illuminate\Support\Facades\Bus;
use Tests\AdminTestCase;

class DebugControllerTest extends AdminTestCase
{
    /**
     * @dataProvider dataRouteUsers
     */
    public function test_権限チェック(string $method, string $name, ?string $prop, int $expected_status)
    {
        if (!is_null($prop)) {
            $this->actingAs($this->{$prop});
        }
        $url = route($name);
        $response = $this->{$method}($url);
        $response->assertStatus($expected_status);
    }

    public function dataRouteUsers()
    {
        foreach ($this->dataUsers() as $key => $value) {
            yield "flushCache/$key" => [
                'postJson', 'api.v2.admin.flushCache', ...$value,
            ];
            yield "phpinfo/$key" => [
                'getJson', 'api.v2.admin.phpinfo', ...$value,
            ];
        }
    }

    public function testDispatch()
    {
        Bus::fake();
        $url = route('api.v2.admin.flushCache');
        $this->actingAs($this->admin);
        $this->postJson($url);
        Bus::assertDispatched(JobUpdateRelated::class);
    }

    /**
     * @dataProvider dataDebug
     */
    public function testDebug($level, ?string $prop, int $expected_status)
    {
        if (!is_null($prop)) {
            $this->actingAs($this->{$prop});
        }
        $url = route('api.v2.admin.debug', ['level' => $level]);
        $response = $this->getJson($url);
        $response->assertStatus($expected_status);
    }

    public function dataDebug()
    {
        foreach (['error', 'warning', 'notice'] as $level) {
            yield "$level/未ログイン" => [$level, null, 401];
            yield "$level/一般ユーザー" => [$level, 'user', 401];
            yield "$level/管理者ユーザー" => [$level, 'admin', 500];
        }
    }
}
