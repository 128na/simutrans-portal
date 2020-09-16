<?php

namespace Tests\Feature\Api\v2\Auth;

use App\Models\User;
use App\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function testReset()
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->assertCredentials(['email' => $user->email, 'password' => 'password']);

        $url = route('api.v2.password.email');

        $res = $this->postJson($url, ['email' => null]);
        $res->assertJsonValidationErrors(['email']);
        $res = $this->postJson($url, ['email' => 'invalid-email']);
        $res->assertJsonValidationErrors(['email']);

        $res = $this->postJson($url, ['email' => 'missing-user@exmaple.com']);
        $res->assertStatus(400);

        Notification::assertNothingSent();

        $res = $this->postJson($url, ['email' => $user->email]);
        $res->assertOK();

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function ($notification, $channels) use (&$token) {
            $token = $notification->token;
            return true;
        });

        $new_password = 'new_password';
        $res = $this->get(route('password.reset', ['token' => $token]));
        $res->assertOK();
        $data = [
            'token' => $token,
            'email' => $user->email,
            'password' => $new_password,
            'password_confirmation' => $new_password,
        ];

        $url = route('password.update');
        $res = $this->post($url, array_merge($data, ['token' => null]));
        $res->assertSessionHasErrors(['token']);
        $res = $this->post($url, array_merge($data, ['token' => 'invalid']));
        $res->assertSessionHasErrors(['email']);

        $res = $this->post($url, array_merge($data, ['email' => null]));
        $res->assertSessionHasErrors(['email']);
        $res = $this->post($url, array_merge($data, ['email' => 'invalid']));
        $res->assertSessionHasErrors(['email']);
        $other_user = User::factory()->create();
        $res = $this->post($url, array_merge($data, ['email' => $other_user->email]));
        $res->assertSessionHasErrors(['email']);

        $res = $this->post($url, array_merge($data, ['password' => null]));
        $res->assertSessionHasErrors(['password']);
        $res = $this->post($url, array_merge($data, ['password' => str_repeat('a', 256)]));
        $res->assertSessionHasErrors(['password']);

        $res = $this->post($url, array_merge($data, ['password_confirmation' => null]));
        $res->assertSessionHasErrors(['password']);
        $res = $this->post($url, array_merge($data, ['password_confirmation' => 'invalid']));
        $res->assertSessionHasErrors(['password']);

        $res = $this->post($url, $data);
        $res->assertRedirect(route('mypage.index'));

        $this->assertInvalidCredentials(['email' => $user->email, 'password' => 'password']);
        $this->assertCredentials(['email' => $user->email, 'password' => $new_password]);
    }
}
