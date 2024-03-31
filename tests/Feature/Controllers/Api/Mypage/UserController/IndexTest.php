<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\UserController;

use App\Models\User;
use Tests\Feature\TestCase;

class IndexTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_未ログイン(): void
    {
        $url = '/api/mypage/user';

        $res = $this->getJson($url);
        $res->assertUnauthorized();
    }

    public function test(): void
    {
        $url = '/api/mypage/user';

        $this->actingAs($this->user);

        $res = $this->getJson($url);
        $res->assertOK();
    }
}
