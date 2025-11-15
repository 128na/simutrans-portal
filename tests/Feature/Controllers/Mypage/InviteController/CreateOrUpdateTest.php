<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\InviteController;

use App\Models\User;
use Illuminate\Support\Str;
use Tests\Feature\TestCase;

final class CreateOrUpdateTest extends TestCase
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

        $testResponse = $this->post($url);
        $testResponse->assertRedirect('/login');
    }

    public function test_招待コードを発行(): void
    {
        $oldCode = $this->user->invitation_code;

        $url = '/mypage/invite';

        $this->actingAs($this->user);

        $testResponse = $this->post($url);
        $testResponse->assertRedirect('/mypage/invite');
        $testResponse->assertSessionHas('status', '招待コードを発行しました');

        $this->user->refresh();
        $this->assertNotEquals($this->user->invitation_code, $oldCode);
        $this->assertNotNull($this->user->invitation_code);
        $this->assertTrue(Str::isUuid($this->user->invitation_code));
    }

    public function test_既存の招待コードを更新(): void
    {
        $oldCode = Str::uuid();
        $this->user->update(['invitation_code' => $oldCode]);

        $url = '/mypage/invite';

        $this->actingAs($this->user);

        $testResponse = $this->post($url);
        $testResponse->assertRedirect('/mypage/invite');

        $this->user->refresh();
        $this->assertNotEquals($this->user->invitation_code, $oldCode);
    }
}
