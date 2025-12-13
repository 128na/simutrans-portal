<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Auth;

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
}
