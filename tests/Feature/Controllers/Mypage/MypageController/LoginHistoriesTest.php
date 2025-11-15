<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\MypageController;

use App\Models\User;
use Tests\Feature\TestCase;

final class LoginHistoriesTest extends TestCase
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
        $url = '/mypage/login-histories';

        $testResponse = $this->get($url);
        $testResponse->assertRedirect('/login');
    }

    public function test_ログイン時にログイン履歴ページを表示(): void
    {
        $url = '/mypage/login-histories';

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertStatus(200);
    }
}
