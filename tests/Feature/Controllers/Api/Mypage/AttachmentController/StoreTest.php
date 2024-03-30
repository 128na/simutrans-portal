<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\AttachmentController;

use App\Jobs\Attachments\UpdateFileInfo;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
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
        $url = '/api/mypage/attachments';

        $this->actingAs($this->user);

        Queue::fake();
        $res = $this->postJson($url, ['files' => [
            UploadedFile::fake()->create('test.zip', 1, 'application/zip'),
        ]]);
        $res->assertOK();
        Queue::assertPushed(UpdateFileInfo::class);
    }

    public function test_未ログイン(): void
    {
        $url = '/api/mypage/attachments';

        $res = $this->postJson($url, []);
        $res->assertUnauthorized();
    }
}
