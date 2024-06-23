<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\ScreenshotController;

use App\Enums\ScreenshotStatus;
use App\Models\User;
use Tests\Feature\TestCase;

final class StoreTest extends TestCase
{
    private User $user;

    #[\Override]
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

        $testResponse = $this->postJson($url, [
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
        $testResponse->assertOk();
    }

    public function test未ログイン(): void
    {
        $url = '/api/mypage/screenshots';

        $testResponse = $this->postJson($url);
        $testResponse->assertUnauthorized();
    }
}
