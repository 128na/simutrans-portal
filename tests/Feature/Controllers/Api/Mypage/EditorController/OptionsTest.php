<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\EditorController;

use App\Models\User;
use Tests\Feature\TestCase;

final class OptionsTest extends TestCase
{
    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_未ログイン(): void
    {
        $url = '/api/mypage/options';

        $testResponse = $this->getJson($url);
        $testResponse->assertUnauthorized();
    }

    public function test(): void
    {
        $url = '/api/mypage/options';

        $this->actingAs($this->user);

        $testResponse = $this->getJson($url);
        $testResponse->assertStatus(200);
    }
}
