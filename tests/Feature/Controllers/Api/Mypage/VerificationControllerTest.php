<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage;

use App\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class VerificationControllerTest extends TestCase
{
    public function test確認メール再送()
    {
        Notification::fake();
        $this->user->fill(['email_verified_at' => null])->save();

        $this->actingAs($this->user);

        $res = $this->postJson('/api/mypage/articles');
        $res->assertForbidden();

        Notification::assertNothingSent();

        $res = $this->postJson('/api/email/resend');
        $res->assertStatus(200);

        /** @var string|null */
        $url = null;
        Notification::assertSentTo($this->user, VerifyEmail::class, function ($notification, $channels) use (&$url) {
            $url = $notification->getVerificationUrl($this->user);

            return true;
        });
        $this->assertNotNull($url);
        $res = $this->get($url);
        $res->assertOk();
        $apiUrl = str_replace('mypage/verify', 'api/email/verify', $url);

        $res = $this->getJson($apiUrl);
        $res->assertStatus(200);

        $res = $this->postJson('/api/mypage/articles');
        $res->assertStatus(422);
    }
}
