<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\AttachmentController;

use App\Models\Attachment;
use App\Models\User;
use Tests\Feature\TestCase;

final class DestroyTest extends TestCase
{
    private User $user;

    private Attachment $attachment;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->attachment = $this->createAttachment($this->user);
    }

    public function test_他人のファイル(): void
    {
        $othersUser = User::factory()->create();
        $attachment = $this->createAttachment($othersUser);
        $url = '/api/mypage/attachments/'.$attachment->id;

        $this->actingAs($this->user);
        $testResponse = $this->deleteJson($url);
        $testResponse->assertStatus(403);
    }

    public function test(): void
    {
        $url = '/api/mypage/attachments/'.$this->attachment->id;

        $this->actingAs($this->user);
        $testResponse = $this->deleteJson($url);
        $testResponse->assertOK();

        $this->assertNull(Attachment::find($this->attachment->id));
    }

    public function test_未ログイン(): void
    {
        $url = '/api/mypage/attachments/'.$this->attachment->id;

        $testResponse = $this->deleteJson($url);
        $testResponse->assertUnauthorized();
    }
}
