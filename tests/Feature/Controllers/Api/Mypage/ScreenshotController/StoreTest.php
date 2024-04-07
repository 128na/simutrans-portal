<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\ScreenshotController;

use App\Enums\ScreenshotStatus;
use App\Models\User;
use Tests\Feature\TestCase;

class StoreTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test(): void
    {
        $url = '/api/mypage/screenshots';
        $this->actingAs($this->user);

        $attachment = $this->createAttachment($this->user);

        $response = $this->postJson($url, [
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
        $url = '/api/mypage/screenshots';

        $response = $this->postJson($url);
        $response->assertUnauthorized();
    }
}
