<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use App\Notifications\ResetPassword;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetPasswordControllerTest extends TestCase
{
    private PasswordBroker $broker;
    private User $user2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->broker = Password::broker();
        $this->user2 = User::factory()->create(['email' => 'other@example.com']);
    }

    /**
     * @dataProvider dataValidation
     */
    public function testValidation(array $data, ?string $error_field)
    {
        Notification::fake();

        $url = route('api.v2.password.email');

        $res = $this->postJson($url, $data);
        if (is_null($error_field)) {
            $res->assertOK();
        } else {
            $res->assertJsonValidationErrors($error_field);
        }
        Notification::assertNothingSent();
    }

    public function dataValidation()
    {
        yield 'emailがnull' => [['email' => null], 'email'];
        yield 'emailが不正' => [['email' => 'invalid-email'], 'email'];
        yield '存在しないemail' => [['email' => 'missing-user@exmaple.com'], null];
    }

    public function test確認メール送信()
    {
        Notification::fake();
        $url = route('api.v2.password.email');
        $res = $this->postJson($url, ['email' => $this->user->email]);
        $res->assertOK();
        Notification::assertSentTo($this->user, ResetPassword::class);
    }

    public function testPWリセット画面表示()
    {
        $res = $this->get(route('password.reset', ['token' => 123]));
        $res->assertOK();

        $token = $this->broker->createToken($this->user);
        $res = $this->get(route('password.reset', ['token' => $token]));
        $res->assertOK();
    }

    /**
     * @dataProvider dataResetValidation
     */
    public function testResetValidation(array $data, ?string $error_field)
    {
        $token = $this->broker->createToken($this->user);
        $data = array_merge([
            'token' => $token,
            'email' => $this->user->email,
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ], $data);
        $url = route('password.update');
        $data = array_merge(['token' => $token], $data);
        $res = $this->postJson($url, $data);

        if (is_null($error_field)) {
            $res->assertOK();

            $this->assertInvalidCredentials(['email' => $this->user->email, 'password' => 'password']);
            $this->assertCredentials(['email' => $this->user->email, 'password' => $data['password']]);
        } else {
            $res->assertJsonValidationErrors($error_field);
            $this->assertCredentials(['email' => $this->user->email, 'password' => 'password']);
            $this->assertInvalidCredentials(['email' => $this->user->email, 'password' => $data['password']]);
        }
    }

    public function dataResetValidation()
    {
        yield 'tokenがnull' => [['token' => null], 'token'];
        yield 'tokenが不正' => [['token' => 'invalid'], 'email'];

        yield 'emailがnull' => [['email' => null], 'email'];
        yield 'emailが不正' => [['email' => 'invalid-email'], 'email'];
        yield 'emailが登録済み' => [['email' => 'other@example.com'], 'email'];

        yield 'passwordがnull' => [['password' => null], 'password'];
        yield 'passwordが256文字以上' => [['password' => str_repeat('a', 256)], 'password'];

        yield 'password確認がnull' => [['password_confirmation' => null], 'password'];
        yield 'password確認が不一致' => [['password_confirmation' => 'invalid'], 'password'];
    }
}
