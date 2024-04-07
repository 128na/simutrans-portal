<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\ScreenshotController;

use App\Enums\ScreenshotStatus;
use App\Models\Screenshot;
use Tests\Feature\TestCase;

final class UpdateTest extends TestCase
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

        $attachment = $this->createAttachment($this->screenshot->user);

        $response = $this->putJson($url, [
            'should_notify' => false,
            'screenshot' => [
                'title' => 'dummy',
                'description' => 'dummy',
                'status' => ScreenshotStatus::Publish->value,
                'attachments' => [[
                    'id' => $attachment->id,
                    'order' => 1,
                    'caption' => '',
                ]],
                'links' => [],
                'articles' => [],
            ]]);
        $response->assertOk();
    }

    public function test未ログイン(): void
    {
        $url = '/api/mypage/screenshots/'.$this->screenshot->id;

        $response = $this->putJson($url);
        $response->assertUnauthorized();
    }
}
