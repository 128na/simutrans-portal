<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages;

use Tests\Feature\TestCase;

class SupportControllerTest extends TestCase
{
    public function test_support(): void
    {
        $testResponse = $this->get(route('support'));

        $testResponse->assertOk();
        $testResponse->assertSee('https://ofuse.me/128na/letter', false);
        $testResponse->assertSee('https://ofuse.me/memberships/4980', false);
        $testResponse->assertSee('https://github.com/sponsors/128na', false);
    }
}
