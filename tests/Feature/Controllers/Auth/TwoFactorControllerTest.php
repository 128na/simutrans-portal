<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use Tests\Feature\TestCase;

class TwoFactorControllerTest extends TestCase
{
    public function test_show_two_factor_guest(): void
    {
        $testResponse = $this->get(route('two-factor.login'));

        $testResponse->assertOk();
    }

    public function test_two_factor有効なユーザーはパスワードのみではログイン完了しない(): void
    {
        $google2fa = app(Google2FA::class);
        $secret = $google2fa->generateSecretKey();

        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'two_factor_secret' => encrypt($secret),
            'two_factor_confirmed_at' => now(),
        ]);

        $loginResponse = $this->postJson('/auth/login', ['email' => $user->email, 'password' => 'password']);

        $loginResponse->assertOk();
        $loginResponse->assertJson(['two_factor' => true]);
        $this->assertGuest();

        $code = $google2fa->getCurrentOtp($secret);
        $confirmResponse = $this->postJson(route('two-factor.login.store'), ['code' => $code]);

        $confirmResponse->assertNoContent();
        $this->assertAuthenticatedAs($user);
    }
}
