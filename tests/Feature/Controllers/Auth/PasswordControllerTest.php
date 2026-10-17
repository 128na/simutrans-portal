<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

class PasswordControllerTest extends TestCase
{
    public function test_show_forgot_password_guest(): void
    {
        $testResponse = $this->get(route('forgot-password'));

        $testResponse->assertOk();
    }

    public function test_show_reset_password_guest(): void
    {
        $token = 'test-reset-token';
        $testResponse = $this->get(route('reset-password', ['token' => $token]));

        $testResponse->assertOk();
    }

    #[DataProvider('invalidResetTokens')]
    public function test_invalid_reset_token_cannot_change_password(string $scenario): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $originalPassword = $user->password;
        $otherPassword = $other->password;
        $rememberTokens = [$user->remember_token, $other->remember_token];
        $broker = Password::broker(config('fortify.passwords'));
        $token = $broker->createToken($user);
        $email = $user->email;
        if ($scenario === 'invalid') {
            $token = 'incorrect-reset-token';
        } elseif ($scenario === 'another user') {
            $email = $other->email;
        } else {
            $this->travel((int) config('auth.passwords.users.expire') + 1)->minutes();
        }
        $this->postJson(route('password.update'), [
            'email' => $email,
            'token' => $token,
            'password' => 'ChangedPassword123!',
            'password_confirmation' => 'ChangedPassword123!',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');

        $this->assertSame($originalPassword, $user->fresh()->password);
        $this->assertSame($otherPassword, $other->fresh()->password);
        $this->assertSame($rememberTokens, [$user->fresh()->remember_token, $other->fresh()->remember_token]);
        $this->travelBack();
    }

    /** @return array<string, array{string}> */
    public static function invalidResetTokens(): array
    {
        return ['invalid' => ['invalid'], 'another user' => ['another user'], 'expired' => ['expired']];
    }

    public function test_successful_reset_rotates_credentials_and_consumes_token_once(): void
    {
        $user = User::factory()->create();
        $rememberToken = $user->remember_token;
        $broker = Password::broker(config('fortify.passwords'));
        $token = $broker->createToken($user);
        $payload = [
            'email' => $user->email,
            'token' => $token,
            'password' => 'ChangedPassword123!',
            'password_confirmation' => 'ChangedPassword123!',
        ];
        $this->postJson(route('password.update'), $payload)->assertOk();
        $updated = $user->fresh();
        $this->assertTrue(Hash::check('ChangedPassword123!', $updated->password));
        $this->assertFalse(Hash::check('password', $updated->password));
        $this->assertNotSame($rememberToken, $updated->remember_token);
        $this->assertFalse($broker->tokenExists($updated, $token));
        $password = $updated->password;
        $rememberToken = $updated->remember_token;
        $this->postJson(route('password.update'), array_replace($payload, [
            'password' => 'ReusedPassword123!',
            'password_confirmation' => 'ReusedPassword123!',
        ]))->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->assertSame($password, $user->fresh()->password);
        $this->assertSame($rememberToken, $user->fresh()->remember_token);
    }
}
