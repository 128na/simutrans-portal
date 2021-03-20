<?php

namespace Tests\Feature\Api\v2\Admin;

use Tests\AdminTestCase;

class UserTest extends AdminTestCase
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
        $this->assertNull($this->article->deleted_at);
        $url = route('api.v2.admin.users.destroy', $this->user);

        if (!is_null($prop)) {
            $this->actingAs($this->{$prop});
        }
        $response = $this->deleteJson($url);
        $response->assertStatus($expected_status);
    }

    public function test_論理削除チェック()
    {
        $this->actingAs($this->admin);

        $url = route('api.v2.admin.users.destroy', $this->user);
        $response = $this->deleteJson($url);

        $this->assertNotNull($this->user->fresh()->deleted_at);
    }
}
