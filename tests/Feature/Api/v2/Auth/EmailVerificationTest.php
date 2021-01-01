<?php

namespace Tests\Feature\Api\v2\Auth;

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    // public function testVerificationEmailAfterRegister()
    // {
    //     Notification::fake();
    //     Notification::assertNothingSent();
    //     $data = [
    //         'name' => 'name',
    //         'email' => 'test@example.com',
    //         'password' => 'password',
    //     ];

    //     $res = $this->postJson(route('api.v2.register', $data));
    //     $res->assertCreated();

    //     $res = $this->postJson(route('api.v2.articles.store'));
    //     $res->assertForbidden();

    //     $user = User::where('email', $data['email'])->first();
    //     $url = null;
    //     Notification::assertSentTo($user, VerifyEmail::class, function ($notification, $channels) use (&$url, $user) {
    //         $url = $notification->getVerificationUrl($user);
    //         return true;
    //     });
    //     $res = $this->get($url);
    //     $res->assertRedirect(route('mypage.index'));

    //     $res = $this->postJson(route('api.v2.articles.store'));
    //     $res->assertStatus(422);
    // }

    public function testVerificationEmailResent()
    {
        Notification::fake();
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user);

        $res = $this->postJson(route('api.v2.articles.store'));
        $res->assertForbidden();

        Notification::assertNothingSent();

        $res = $this->postJson(route('api.v2.verification.resend'));
        $res->assertStatus(200);

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
}
