<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Attachment;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\TestCase;
use Tests\Unit\Services\Image\BuildsImagesWithMetadata;

class StripImageMetadataCommandTest extends TestCase
{
    use BuildsImagesWithMetadata;

    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Storage::fake('local');
        $this->user = User::factory()->create();
    }

    public function test_指定がなければ数えるだけで書き換えない(): void
    {
        $content = $this->jpegWithMetadata();
        $attachment = $this->createStored('photo.jpg', $content);
        $this->createStored('clean.png', $this->plainPng());
        $this->createStored('addon.zip', "PK\x03\x04Secret");

        $this->artisan('attachment:strip-image-metadata')
            ->expectsOutputToContain('画像: 2件')
            ->expectsOutputToContain('書き換え対象: 1件')
            ->assertSuccessful();

        $this->assertSame($content, Storage::disk('public')->get($attachment->path));
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_executeで原本を退避してから書き換える(): void
    {
        $content = $this->jpegWithMetadata();
        $attachment = $this->createStored('photo.jpg', $content);
        $backupPath = 'image-metadata-backup/'.$attachment->path;

        $this->artisan('attachment:strip-image-metadata', ['--execute' => true])
            ->expectsOutputToContain('書き換えた画像: 1件')
            ->assertSuccessful();

        $stored = (string) Storage::disk('public')->get($attachment->path);
        $this->assertArrayNotHasKey('GPSLatitude', $this->readExif($stored));
        $this->assertStringNotContainsString('Secret', $stored);
        $this->assertSame(strlen($stored), $attachment->refresh()->size);
        $this->assertSame($content, Storage::disk('local')->get($backupPath));
        $this->assertSame([$attachment->path], Storage::disk('public')->allFiles());

        // 2回目は対象がなく、退避した原本も変わらない
        $this->artisan('attachment:strip-image-metadata', ['--execute' => true])
            ->expectsOutputToContain('書き換えた画像: 0件')
            ->assertSuccessful();
        $this->assertSame($content, Storage::disk('local')->get($backupPath));
    }

    public function test_idで対象を絞れる(): void
    {
        $content = $this->jpegWithMetadata();
        $target = $this->createStored('a.jpg', $content);
        $other = $this->createStored('b.jpg', $content);

        $this->artisan('attachment:strip-image-metadata', ['--execute' => true, '--id' => [$target->id]])
            ->assertSuccessful();

        $this->assertNotSame($content, Storage::disk('public')->get($target->path));
        $this->assertSame($content, Storage::disk('public')->get($other->path));
    }

    public function test_処理できない画像は残して失敗を返し他は続ける(): void
    {
        $broken = "\x89PNG\r\n\x1a\n\x00\x00\x00\x0DIHDR".pack('NNC5', 1, 1, 8, 2, 0, 0, 0)."CRC!\x00\x00\xFF\xFFeXIf";
        $brokenAttachment = $this->createStored('broken.png', $broken);
        $attachment = $this->createStored('photo.jpg', $this->jpegWithMetadata());
        Attachment::factory()->create(['user_id' => $this->user->id, 'path' => 'user/missing.jpg']);

        $this->artisan('attachment:strip-image-metadata', ['--execute' => true])
            ->expectsOutputToContain('実体のないファイル: 1件')
            ->expectsOutputToContain('処理できなかった添付ファイルID: '.$brokenAttachment->id)
            ->assertFailed();

        $this->assertSame($broken, Storage::disk('public')->get($brokenAttachment->path));
        $this->assertStringNotContainsString('Secret', (string) Storage::disk('public')->get($attachment->path));
    }

    private function createStored(string $name, string $content): Attachment
    {
        $path = 'user/'.$this->user->id.'/'.$name;
        Storage::disk('public')->put($path, $content);

        return Attachment::factory()->create([
            'user_id' => $this->user->id,
            'path' => $path,
            'original_name' => $name,
            'size' => strlen($content),
        ]);
    }
}
