<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages;

use Tests\Feature\TestCase;

class SocialControllerTest extends TestCase
{
    public function test_social(): void
    {
        $testResponse = $this->get(route('social'));

        $testResponse->assertOk();
    }
}
