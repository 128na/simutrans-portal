<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\ScreenshotController;

use App\Models\Screenshot;
use Tests\Feature\TestCase;

final class IndexTest extends TestCase
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
        $url = '/api/mypage/screenshots';
        $this->actingAs($this->screenshot->user);

        $testResponse = $this->getJson($url);
        $testResponse->assertOk();
        $testResponse->assertSee($this->screenshot->title);
    }

    public function test未ログイン(): void
    {
        $url = '/api/mypage/screenshots';

        $testResponse = $this->getJson($url);
        $testResponse->assertUnauthorized();
    }
}
