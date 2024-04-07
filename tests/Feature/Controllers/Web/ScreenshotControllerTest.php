<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Web;

use App\Models\Screenshot;
use Tests\Feature\TestCase;

class ScreenshotControllerTest extends TestCase
{
    public function testIndex(): void
    {
        $testResponse = $this->get(route('screenshots.index'));

        $testResponse->assertOk();
    }

    public function testShow(): void
    {
        $screenshot = Screenshot::factory()->create();
        $testResponse = $this->get(route('screenshots.show', $screenshot));

        $testResponse->assertOk();
    }
}
