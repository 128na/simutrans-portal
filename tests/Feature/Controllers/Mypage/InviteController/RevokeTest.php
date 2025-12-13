<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\InviteController;

use App\Models\User;
use Illuminate\Support\Str;
use Tests\Feature\TestCase;

class RevokeTest extends TestCase
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

        $testResponse = $this->delete($url);
        $testResponse->assertRedirect('/login');
    }

    public function test_招待コードを削除(): void
    {
        $this->user->update(['invitation_code' => Str::uuid()]);
        $this->assertNotNull($this->user->invitation_code);

        $url = '/mypage/invite';

        $this->actingAs($this->user);

        $testResponse = $this->delete($url);
        $testResponse->assertRedirect('/mypage/invite');
        $testResponse->assertSessionHas('status', '招待コードを削除しました');

        $this->user->refresh();
        $this->assertNull($this->user->invitation_code);
    }

    public function test_招待コードがない場合でも削除できる(): void
    {
        $this->user->update(['invitation_code' => null]);

        $url = '/mypage/invite';

        $this->actingAs($this->user);

        $testResponse = $this->delete($url);
        $testResponse->assertRedirect('/mypage/invite');
        $testResponse->assertSessionHas('status', '招待コードを削除しました');

        $this->user->refresh();
        $this->assertNull($this->user->invitation_code);
    }
}
