<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class VerificationControllerTest extends TestCase
{
    public function test登録後の確認メール()
    {
        Config::set('app.register_restriction', false);
        Notification::fake();
        Notification::assertNothingSent();
        $data = [
            'name' => 'name',
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        $res = $this->postJson(route('api.v2.register', $data));
        $res->assertCreated();

        $res = $this->postJson(route('api.v2.articles.store'));
        $res->assertForbidden();

        $user = User::where('email', $data['email'])->first();
        $url = null;
        Notification::assertSentTo($user, VerifyEmail::class, function ($notification, $channels) use (&$url, $user) {
            $url = $notification->getVerificationUrl($user);

            return true;
        });
        $res = $this->get($url);
        $res->assertRedirect(route('mypage.index'));

        $res = $this->postJson(route('api.v2.articles.store'));
        $res->assertStatus(422);
    }

    public function test確認メール再送()
    {
        Notification::fake();
        $this->user->fill(['email_verified_at' => null])->save();

        $this->actingAs($this->user);

        $res = $this->postJson(route('api.v2.articles.store'));
        $res->assertForbidden();

        Notification::assertNothingSent();

        $res = $this->postJson(route('api.v2.verification.resend'));
        $res->assertStatus(200);

        $url = null;
        Notification::assertSentTo($this->user, VerifyEmail::class, function ($notification, $channels) use (&$url) {
            $url = $notification->getVerificationUrl($this->user);

            return true;
        });
        $res = $this->get($url);
        $res->assertRedirect(route('mypage.index'));

        $res = $this->postJson(route('api.v2.articles.store'));
        $res->assertStatus(422);
    }
}
