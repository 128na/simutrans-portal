<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\AttachmentController;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;
use Tests\Unit\Services\Image\BuildsImagesWithMetadata;

class StoreImageMetadataTest extends TestCase
{
    use BuildsImagesWithMetadata;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Queue::fake();
        $this->actingAs(User::factory()->create());
    }

    #[DataProvider('images')]
    public function test_位置情報入りの画像は撮影情報を除いて保存する(string $name): void
    {
        $content = $name === 'photo.jpg' ? $this->jpegWithMetadata(orientation: 6) : $this->pngWithMetadata();
        $this->assertStringContainsString('SecretCam', $content);

        $this->postJson('/api/v2/attachments', ['file' => $this->realUpload($name, $content)])
            ->assertCreated();

        $attachment = Attachment::sole();
        $stored = (string) Storage::disk('public')->get($attachment->path);
        $this->assertStringNotContainsString('Secret', $stored);
        $this->assertSame(strlen($stored), $attachment->size);
        if ($name === 'photo.jpg') {
            $exif = $this->readExif($stored);
            $this->assertArrayNotHasKey('GPSLatitude', $exif);
            $this->assertSame(6, $exif['Orientation']);
        }
    }

    public static function images(): array
    {
        return ['jpeg' => ['photo.jpg'], 'png' => ['photo.png']];
    }

    public function test_撮影情報を除けない壊れた画像は保存せず拒否する(): void
    {
        $file = $this->realUpload('broken.jpg', "\xFF\xD8\xFF\xE0\x00\x10JFIF\0\x01\x01\0\0\x01\0\x01\0\0\xFF\xE1\xFF\xFFExif");

        $this->postJson('/api/v2/attachments', ['file' => $file])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('file');

        $this->assertDatabaseCount('attachments', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    /** 偽ファイルは差し替え前のサイズを返し続けるため、実際のアップロードと同じ型で送る */
    private function realUpload(string $name, string $content): UploadedFile
    {
        $path = (string) tempnam(sys_get_temp_dir(), 'metadata-test-');
        file_put_contents($path, $content);
        $this->beforeApplicationDestroyed(static function () use ($path): void {
            unlink($path);
        });

        return new UploadedFile($path, $name, null, null, true);
    }
}
