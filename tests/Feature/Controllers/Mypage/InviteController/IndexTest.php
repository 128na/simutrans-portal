<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\InviteController;

use App\Models\User;
use Illuminate\Support\Str;
use Tests\Feature\TestCase;

class IndexTest extends TestCase
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
        $url = '/mypage/invite';

        $testResponse = $this->get($url);
        $testResponse->assertRedirect('/login');
    }

    public function test_ログイン時に招待ページを表示(): void
    {
        $url = '/mypage/invite';

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertStatus(200);
    }

    public function test_招待コードを持つユーザーの情報が表示される(): void
    {
        $this->user->update(['invitation_code' => Str::uuid()]);

        $url = '/mypage/invite';

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertStatus(200);
    }
}
