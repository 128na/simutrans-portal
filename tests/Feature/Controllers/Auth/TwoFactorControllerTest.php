<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use PragmaRX\Google2FA\Google2FA;
use Tests\Feature\TestCase;

class TwoFactorControllerTest extends TestCase
{
    public function test_invalid_otp_is_rejected_and_sixth_attempt_is_throttled(): void
    {
        $google2fa = app(Google2FA::class);
        $secret = $google2fa->generateSecretKey();
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'two_factor_secret' => encrypt($secret),
            'two_factor_confirmed_at' => now(),
        ]);
        Notification::fake();
        $this->freezeTime();
        $invalidCode = '000000';
        while ($google2fa->verifyKey($secret, $invalidCode, 1)) {
            $invalidCode = str_pad((string) ((int) $invalidCode + 1), 6, '0', STR_PAD_LEFT);
        }

        $this->postJson('/auth/login', ['email' => $user->email, 'password' => 'password'])
            ->assertOk()
            ->assertJson(['two_factor' => true]);
        $this->assertGuest();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson(route('two-factor.login.store'), ['code' => $invalidCode])
                ->assertUnprocessable()
                ->assertJsonValidationErrors('code');
            $this->assertGuest();
            $this->assertSame($user->id, session('login.id'));
        }

        $this->postJson(route('two-factor.login.store'), ['code' => $invalidCode])
            ->assertTooManyRequests();
        $this->assertGuest();
    }

    public function test_recovery_code_is_consumed_and_cannot_be_reused_in_a_new_challenge(): void
    {
        $recoveryCode = 'recovery-once';
        $unusedCode = 'recovery-unused';
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'two_factor_secret' => encrypt(app(Google2FA::class)->generateSecretKey()),
            'two_factor_recovery_codes' => encrypt(json_encode([$recoveryCode, $unusedCode], JSON_THROW_ON_ERROR)),
            'two_factor_confirmed_at' => now(),
        ]);
        Notification::fake();

        $this->postJson('/auth/login', ['email' => $user->email, 'password' => 'password'])
            ->assertOk()
            ->assertJson(['two_factor' => true]);
        $this->assertGuest();
        $this->postJson(route('two-factor.login.store'), ['recovery_code' => $recoveryCode])
            ->assertNoContent();
        $this->assertAuthenticatedAs($user);
        $codesAfterUse = $user->fresh()->recoveryCodes();
        $this->assertNotContains($recoveryCode, $codesAfterUse);
        $this->assertContains($unusedCode, $codesAfterUse);
        $this->assertCount(2, $codesAfterUse);

        $this->postJson('/auth/logout')->assertNoContent();
        $this->assertGuest();
        $this->postJson('/auth/login', ['email' => $user->email, 'password' => 'password'])
            ->assertOk()
            ->assertJson(['two_factor' => true]);
        $this->assertGuest();
        $this->assertSame($user->id, session('login.id'));

        $this->postJson(route('two-factor.login.store'), ['recovery_code' => $recoveryCode])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('recovery_code');
        $this->assertGuest();
        $this->assertSame($codesAfterUse, $user->fresh()->recoveryCodes());
    }

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
