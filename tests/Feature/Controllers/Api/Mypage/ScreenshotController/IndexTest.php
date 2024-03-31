<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\ScreenshotController;

use App\Models\Screenshot;
use Tests\Feature\TestCase;

class IndexTest extends TestCase
{
    private Screenshot $screenshot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->screenshot = Screenshot::factory()->create();
    }

    public function test(): void
    {
        $url = '/api/mypage/screenshots';
        $this->actingAs($this->screenshot->user);

        $response = $this->getJson($url);
        $response->assertOk();
        $response->assertSee($this->screenshot->title);
    }

    public function test未ログイン(): void
    {
        $url = '/api/mypage/screenshots';

        $response = $this->getJson($url);
        $response->assertUnauthorized();
    }
}
