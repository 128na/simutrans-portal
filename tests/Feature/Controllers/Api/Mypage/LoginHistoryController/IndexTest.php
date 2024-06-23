<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\LoginHistoryController;

use App\Models\User\LoginHistory;
use Tests\Feature\TestCase;

final class IndexTest extends TestCase
{
    private LoginHistory $loginHistory;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginHistory = LoginHistory::factory()->create();
    }

    public function test(): void
    {
        $url = '/api/mypage/login_histories';
        $this->actingAs($this->loginHistory->user);

        $testResponse = $this->getJson($url);
        $testResponse->assertOk();
        $testResponse->assertSee($this->loginHistory->ip);
    }

    public function test未ログイン(): void
    {
        $url = '/api/mypage/login_histories';

        $testResponse = $this->getJson($url);
        $testResponse->assertUnauthorized();
    }
}
