<?php

namespace Tests\Feature\Http\Controllers\Api\v2\Admin;

use App\Jobs\Article\JobUpdateRelated;
use Illuminate\Support\Facades\Bus;
use Tests\AdminTestCase;

class UserControllerTest extends AdminTestCase
{
    /**
     * @dataProvider dataUsers
     */
    public function test_ユーザー一覧_権限チェック(?string $prop, int $expected_status)
    {
        if (!is_null($prop)) {
            $this->actingAs($this->{$prop});
        }
        $url = route('api.v2.admin.users.index');

        $response = $this->getJson($url);
        $response->assertStatus($expected_status);
    }

    /**
     * @dataProvider dataUsers
     */
    public function test_ユーザー削除_権限チェック(?string $prop, int $expected_status)
    {
        Bus::fake();
        $this->assertNull($this->article->deleted_at);
        $url = route('api.v2.admin.users.destroy', $this->user);

        if (!is_null($prop)) {
            $this->actingAs($this->{$prop});
        }
        $response = $this->deleteJson($url);
        $response->assertStatus($expected_status);
        $response->assertStatus($expected_status);

        if ($expected_status === 200) {
            Bus::assertDispatched(JobUpdateRelated::class);
        } else {
            Bus::assertNotDispatched(JobUpdateRelated::class);
        }
    }

    public function test_論理削除チェック()
    {
        $this->actingAs($this->admin);

        $url = route('api.v2.admin.users.destroy', $this->user);
        $response = $this->deleteJson($url);

        $this->assertNotNull($this->user->fresh()->deleted_at);
    }
}
