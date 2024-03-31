<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\InvitationCodeController;

use App\Enums\ControllOptionKey;
use App\Models\ControllOption;
use App\Models\User;
use App\Notifications\UserInvited;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\Feature\TestCase;

class RegisterTest extends TestCase
{
    private User $inviterUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inviterUser = User::factory()->create(['invitation_code' => Str::uuid()]);
    }

    public function test(): void
    {
        Notification::fake();
        Event::fake();
        $this->assertGuest();

        $testResponse = $this->postJson(
            '/api/mypage/invite/'.$this->inviterUser->invitation_code,
            [
                'name' => 'example',
                'email' => 'example@example.com',
                'password' => 'example123456',
            ]
        );

        $testResponse->assertCreated();
        $this->assertAuthenticated();
        Event::assertDispatched(Registered::class);
        Notification::assertSentTo([$this->inviterUser], UserInvited::class);
    }

    public function test機能無効(): void
    {
        ControllOption::updateOrCreate(['key' => ControllOptionKey::InvitationCode], ['value' => false]);

        $testResponse = $this->postJson(
            '/api/mypage/invite/'.$this->inviterUser->invitation_code,
            []
        );
        $testResponse->assertStatus(403);
    }
}
