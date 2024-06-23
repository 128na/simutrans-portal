<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\ScreenshotController;

use App\Models\Screenshot;
use Tests\Feature\TestCase;

final class DestroyTest extends TestCase
{
    private Screenshot $screenshot;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->screenshot = Screenshot::factory()->create();
    }

    public function test(): void
    {
        $url = '/api/mypage/screenshots/'.$this->screenshot->id;
        $this->actingAs($this->screenshot->user);

        $testResponse = $this->deleteJson($url);
        $testResponse->assertOk();
    }

    public function test未ログイン(): void
    {
        $url = '/api/mypage/screenshots/'.$this->screenshot->id;

        $testResponse = $this->deleteJson($url);
        $testResponse->assertUnauthorized();
    }
}
