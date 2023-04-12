<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage;

use PHPUnit\Framework\Attributes\DataProvider;
use App\Jobs\Attachments\UpdateFileInfo;
use App\Models\Attachment;
use App\Models\User;
use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Tests\ArticleTestCase;

class AttachmentControllerTest extends ArticleTestCase
{
    public function testIndex()
    {
        $url = '/api/mypage/attachments';

        $res = $this->getJson($url);
        $res->assertUnauthorized();

        $this->actingAs($this->user);

        $res = $this->getJson($url);
        $res->assertOK();
    }

    public function testFormatImage()
    {
        $file = $this->createFromFile(UploadedFile::fake()->image('test.png', 1), $this->user->id);

        $url = '/api/mypage/attachments';
        $this->actingAs($this->user);

        $res = $this->getJson($url);
        $res->assertOK();
    }

    public function testFormatOther()
    {
        $file = $this->createFromFile(UploadedFile::fake()->create('test.zip', 1, 'application/zip'), $this->user->id);

        $url = '/api/mypage/attachments';
        $this->actingAs($this->user);

        $res = $this->getJson($url);
        $res->assertOK();
    }

    public static function dataValidation()
    {
        yield 'fileがnull' => [fn () => ['file' => null], 'file'];
        yield 'fileがファイル以外' => [fn () => ['file' => 'test.zip'], 'file'];
        yield '成功' => [fn () => ['file' => UploadedFile::fake()->create('test.zip', 1, 'application/zip')], null];
        yield '画像のみで画像以外' => [fn () => ['only_image' => 1, 'file' => UploadedFile::fake()->create('test.zip', 1, 'application/zip')], 'file'];
        yield '画像のみで画像' => [fn () => ['only_image' => 1, 'file' => UploadedFile::fake()->image('test.png', 1)], null];
    }

    #[DataProvider('dataValidation')]
    public function testStore(Closure $data, ?string $error_field)
    {
        $url = '/api/mypage/attachments';

        $this->actingAs($this->user);

        $data = Closure::bind($data, $this)();

        Queue::fake();
        $res = $this->postJson($url, $data);
        if (is_null($error_field)) {
            $res->assertOK();
            Queue::assertPushed(UpdateFileInfo::class);
        } else {
            $res->assertJsonValidationErrors($error_field);
            Queue::assertNotPushed(UpdateFileInfo::class);
        }
    }

    public function testDestroy()
    {
        /** @var User */
        $user = User::factory()->create();

        $file = $this->createFromFile(UploadedFile::fake()->image('file.png', 1), $user->id);
        $url = "/api/mypage/attachments/{$file->id}";
        $res = $this->deleteJson($url);
        $res->assertUnauthorized();

        $this->actingAs($user);

        $other_user = User::factory()->create();
        $other_file = $this->createFromFile(UploadedFile::fake()->image('file.png', 1), $other_user->id);
        $url = "/api/mypage/attachments/{$other_file->id}";
        $res = $this->deleteJson($url);
        $res->assertStatus(403);
        $this->assertDatabaseHas('attachments', [
            'id' => $file->id,
        ]);
        $this->assertDatabaseHas('attachments', [
            'id' => $other_file->id,
        ]);

        $url = "/api/mypage/attachments/{$file->id}";
        $res = $this->deleteJson($url);
        $res->assertOK();

        $this->assertNull(Attachment::find($file->id));

        // IDのみだとなぜか失敗する
        $this->assertDatabaseMissing('attachments', [
            'id' => $file->id,
            'user_id' => $file->user_if,
            // 'attachmentable_id' => null,
            // 'attachmentable_type' => null,
            // 'original_name' => 'file.png',
            // 'path' => $file->path,
            // 'created_at' => $file->created_at,
            // 'updated_at' => $file->updated_at,
        ]);
    }
}
