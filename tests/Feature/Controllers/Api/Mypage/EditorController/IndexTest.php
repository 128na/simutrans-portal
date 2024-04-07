<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\EditorController;

use App\Models\User;
use Tests\Feature\TestCase;

final class IndexTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_未ログイン(): void
    {
        $url = '/api/mypage/articles';

        $response = $this->getJson($url);
        $response->assertUnauthorized();
    }

    public function test(): void
    {
        $url = '/api/mypage/articles';

        $this->actingAs($this->user);

        $response = $this->getJson($url);
        $response->assertStatus(200);
    }
}
