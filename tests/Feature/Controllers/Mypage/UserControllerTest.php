<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage;

use App\Models\User;
use Tests\Feature\TestCase;

final class UserControllerTest extends TestCase
{
    public function test_show_login_guest(): void
    {
        $testResponse = $this->get(route('login'));

        $testResponse->assertOk();
    }

    public function test_show_login_authenticated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $testResponse = $this->get(route('login'));

        $testResponse->assertRedirect(route('mypage.index'));
    }

    public function test_show_two_factor_guest(): void
    {
        $testResponse = $this->get(route('two-factor.login'));

        $testResponse->assertOk();
    }

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
}
