<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\AttachmentController;

use App\Actions\StoreAttachment\ConvertFailedException;
use App\Actions\StoreAttachment\Store;
use App\Jobs\Attachments\JobGenerateThumbnail;
use App\Jobs\Attachments\UpdateFileInfo;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\Feature\TestCase;

class StoreTest extends TestCase
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

    public function test_画像をアップロードするとサムネイル生成ジョブがディスパッチされる(): void
    {
        $url = '/api/v2/attachments';

        Storage::fake('public');
        $this->actingAs($this->user);

        Queue::fake();
        $testResponse = $this->postJson($url, ['file' => UploadedFile::fake()->image('test.jpg', 100, 100)]);
        $testResponse->assertCreated();

        $attachment = Attachment::sole();
        $this->assertSame($this->user->id, $attachment->user_id);
        $this->assertSame('test.jpg', $attachment->original_name);
        Storage::disk('public')->assertExists($attachment->path);

        Queue::assertPushed(JobGenerateThumbnail::class, fn ($job): bool => $job->attachment->id === $attachment->id);
    }

    public function test_画像変換に失敗した場合はファイルとして保存されサムネイル生成ジョブはディスパッチされない(): void
    {
        $url = '/api/v2/attachments';

        Storage::fake('public');
        $this->actingAs($this->user);

        $fallbackPath = 'user/'.$this->user->id.'/fallback.jpg';

        $mockDisk = Mockery::mock(FilesystemAdapter::class);
        $mockDisk->shouldReceive('put')->once()->andThrow(new ConvertFailedException('convert failed'));
        $mockDisk->shouldReceive('put')->once()->andReturn($fallbackPath);
        $this->app->instance(Store::class, new Store($mockDisk));

        Queue::fake();
        $testResponse = $this->postJson($url, ['file' => UploadedFile::fake()->image('test.jpg', 100, 100)]);
        $testResponse->assertCreated();

        $attachment = Attachment::sole();
        $this->assertSame($this->user->id, $attachment->user_id);
        $this->assertSame($fallbackPath, $attachment->path);
        $this->assertSame('test.jpg', $attachment->original_name);

        Queue::assertNotPushed(JobGenerateThumbnail::class);
    }

    public function test_未ログイン(): void
    {
        $url = '/api/v2/attachments';

        $testResponse = $this->postJson($url, []);
        $testResponse->assertUnauthorized();
    }

    public function test_許可されていない拡張子は拒否される(): void
    {
        $url = '/api/v2/attachments';

        $this->actingAs($this->user);

        $testResponse = $this->postJson($url, ['file' => UploadedFile::fake()->create('shell.php', 1)]);
        $testResponse->assertUnprocessable();
        $testResponse->assertJsonValidationErrors('file');
    }
}
