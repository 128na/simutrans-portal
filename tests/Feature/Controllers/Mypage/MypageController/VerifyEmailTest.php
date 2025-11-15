<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\MypageController;

use App\Models\User;
use Tests\Feature\TestCase;

final class VerifyEmailTest extends TestCase
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
        $url = '/mypage/verify-email';

        $testResponse = $this->get($url);
        $testResponse->assertRedirect('/login');
    }

    public function test_ログイン時にメール認証ページを表示(): void
    {
        $url = '/mypage/verify-email';

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertStatus(200);
    }
}
