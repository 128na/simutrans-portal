<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\DashboardController;

use App\Models\User;
use Tests\Feature\TestCase;

class VerifyNoticeTest extends TestCase
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
        $url = '/mypage/verify-required';

        $testResponse = $this->get($url);
        $testResponse->assertRedirect('/login');
    }

    public function test_ログイン時にverify_emailにリダイレクト(): void
    {
        $url = '/mypage/verify-required';

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertRedirect('/mypage/verify-email');
        $testResponse->assertSessionHas('error', 'この機能を使うにはメールアドレスの認証を完了させる必要があります。');
    }
}
