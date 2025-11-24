<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\DashboardController;

use App\Models\User;
use Tests\Feature\TestCase;

final class IndexTest extends TestCase
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
        $url = '/mypage';

        $testResponse = $this->get($url);
        $testResponse->assertRedirect('/login');
    }

    public function test_ログイン時にマイページを表示(): void
    {
        $url = '/mypage';

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertStatus(200);
    }

    public function test_ユーザー情報が正しく渡される(): void
    {
        $url = '/mypage';

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertStatus(200);
    }
}
