<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Auth;

use Tests\Feature\TestCase;

class TwoFactorControllerTest extends TestCase
{
    public function test_show_two_factor_guest(): void
    {
        $testResponse = $this->get(route('two-factor.login'));

        $testResponse->assertOk();
    }
}
