<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\AttachmentController;

use App\Jobs\Attachments\UpdateFileInfo;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
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
        $url = '/api/mypage/attachments';

        $this->actingAs($this->user);

        Queue::fake();
        $testResponse = $this->postJson($url, ['files' => [
            UploadedFile::fake()->create('test.zip', 1, 'application/zip'),
        ]]);
        $testResponse->assertOK();
        Queue::assertPushed(UpdateFileInfo::class);
    }

    public function test_未ログイン(): void
    {
        $url = '/api/mypage/attachments';

        $testResponse = $this->postJson($url, []);
        $testResponse->assertUnauthorized();
    }
}
