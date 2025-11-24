<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages;

use Tests\Feature\TestCase;

final class TopControllerTest extends TestCase
{
    public function test_top(): void
    {
        $testResponse = $this->get(route('index'));

        $testResponse->assertOk();
    }
}
