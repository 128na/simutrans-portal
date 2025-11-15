<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\AttachmentController;

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
        $url = '/api/v2/attachments';

        $this->actingAs($this->user);

        Queue::fake();
        $testResponse = $this->postJson($url, ['file' => UploadedFile::fake()->create('test.zip', 1, 'application/zip')]);
        $testResponse->assertCreated();
        Queue::assertPushed(UpdateFileInfo::class);
    }

    public function test_未ログイン(): void
    {
        $url = '/api/v2/attachments';

        $testResponse = $this->postJson($url, []);
        $testResponse->assertUnauthorized();
    }
}
