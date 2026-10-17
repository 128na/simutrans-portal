<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\AttachmentController;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\TestCase;

class DestroyTest extends TestCase
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
        $url = '/api/v2/attachments/'.$attachment->id;

        $this->actingAs($this->user);
        $testResponse = $this->deleteJson($url);
        $testResponse->assertStatus(403);
    }

    public function test_他人のファイルは削除されずに残る(): void
    {
        Storage::fake('public');
        $othersUser = User::factory()->create();
        $attachment = $this->createAttachment($othersUser);
        $disk = Storage::disk('public');
        $disk->put($attachment->path, 'Owner attachment contents');
        $original = $attachment->fresh()->getRawOriginal();
        $files = $disk->allFiles();
        $contents = $disk->get($attachment->path);
        $url = '/api/v2/attachments/'.$attachment->id;

        $this->actingAs($this->user);
        $this->deleteJson($url)->assertForbidden();

        $this->assertSame($original, $attachment->fresh()->getRawOriginal());
        $this->assertSame($files, $disk->allFiles());
        $this->assertSame($contents, $disk->get($attachment->path));
    }

    public function test(): void
    {
        $url = '/api/v2/attachments/'.$this->attachment->id;

        $this->actingAs($this->user);
        $testResponse = $this->deleteJson($url);
        $testResponse->assertOK();

        $this->assertNull(Attachment::find($this->attachment->id));
    }

    public function test_未ログイン(): void
    {
        $url = '/api/v2/attachments/'.$this->attachment->id;

        $testResponse = $this->deleteJson($url);
        $testResponse->assertUnauthorized();
    }
}
