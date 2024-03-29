<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\ScreenshotController;

use App\Models\Screenshot;
use Tests\Feature\TestCase;

class DestroyTest extends TestCase
{
    private Screenshot $screenshot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->screenshot = Screenshot::factory()->create();
    }

    public function test(): void
    {
        $url = '/api/mypage/screenshots/'.$this->screenshot->id;
        $this->actingAs($this->screenshot->user);

        $response = $this->deleteJson($url);
        $response->assertOk();
    }

    public function test未ログイン(): void
    {
        $url = '/api/mypage/screenshots/'.$this->screenshot->id;

        $response = $this->deleteJson($url);
        $response->assertUnauthorized();
    }
}
