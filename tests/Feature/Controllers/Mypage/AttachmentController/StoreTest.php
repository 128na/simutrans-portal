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
use Illuminate\Validation\ValidationException;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
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
        $mockDisk->shouldReceive('putFileAs')->once()->andThrow(new ConvertFailedException('convert failed'));
        $mockDisk->shouldReceive('putFileAs')->once()->andReturn($fallbackPath);
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

    #[DataProvider('unsafeContent')]
    public function test_disguised_active_content_is_rejected_without_side_effects(string $content): void
    {
        Storage::fake('public');
        Queue::fake();
        $this->actingAs($this->user)
            ->postJson('/api/v2/attachments', ['file' => $this->realUpload('notes.txt', $content)])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('file');

        $this->assertDatabaseCount('attachments', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
        Queue::assertNothingPushed();
    }

    public static function unsafeContent(): array
    {
        return [
            'html' => ['<!DOCTYPE html><html><body><script>alert(1)</script></body></html>'],
            'php' => ['<?php echo "unsafe";'],
            'svg' => ['<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"></svg>'],
        ];
    }

    #[DataProvider('safeFiles')]
    public function test_files_keep_safe_original_extension_and_random_basename(string $name, string $content, string $extension): void
    {
        Storage::fake('public');
        Queue::fake();
        $this->actingAs($this->user)
            ->postJson('/api/v2/attachments', ['file' => $this->realUpload($name, $content)])
            ->assertCreated();

        $attachment = Attachment::sole();
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]{40}\\.'.$extension.'$/', basename($attachment->path));
        $this->assertSame($name, $attachment->original_name);
        $this->assertSame($content, Storage::disk('public')->get($attachment->path));
    }

    public static function safeFiles(): array
    {
        return [
            'pak' => ['addon.pak', "Simutrans object file\n\x00\x01", 'pak'],
            'tab' => ['simuconf.tab', "# configuration\nframes_per_second = 25\n", 'tab'],
            'uppercase zip' => ['addon.ZIP', "PK\x03\x04", 'zip'],
            'plain text' => ['readme.txt', 'Read this before installation.', 'txt'],
        ];
    }

    public function test_action_rejects_dangerous_upload_without_form_request(): void
    {
        Storage::fake('public');
        $store = new Store(Storage::disk('public'));
        try {
            $store($this->user, $this->realUpload('readme.txt', '<html><body>unsafe</body></html>'));
            $this->fail('Unsafe content was stored.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('file', $exception->errors());
        }

        $this->assertDatabaseCount('attachments', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    private function realUpload(string $name, string $content): UploadedFile
    {
        $fake = UploadedFile::fake()->createWithContent($name, $content);
        $path = tempnam(sys_get_temp_dir(), 'attachment-test-');
        copy($fake->getPathname(), $path);
        $this->beforeApplicationDestroyed(static function () use ($path): void {
            unlink($path);
        });

        return new UploadedFile($path, $name, 'application/octet-stream', null, true);
    }
}
