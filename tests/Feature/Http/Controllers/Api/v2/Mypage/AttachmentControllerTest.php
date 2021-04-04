<?php

namespace Tests\Feature\Http\Controllers\Api\v2\Mypage;

use App\Models\Attachment;
use App\Models\User;
use Closure;
use Illuminate\Http\UploadedFile;
use Tests\ArticleTestCase;

class AttachmentControllerTest extends ArticleTestCase
{
    public function testIndex()
    {
        $url = route('api.v2.attachments.index');

        $res = $this->getJson($url);
        $res->assertUnauthorized();

        $this->actingAs($this->user);

        $res = $this->getJson($url);
        $res->assertOK();
    }

    public function testFormatImage()
    {
        $file = $this->createFromFile(UploadedFile::fake()->image('test.png', 1), $this->user->id);

        $url = route('api.v2.attachments.index');
        $this->actingAs($this->user);

        $res = $this->getJson($url);
        $res->assertOK();
    }

    public function testFormatOther()
    {
        $file = $this->createFromFile(UploadedFile::fake()->create('test.zip', 1, 'application/zip'), $this->user->id);

        $url = route('api.v2.attachments.index');
        $this->actingAs($this->user);

        $res = $this->getJson($url);
        $res->assertOK();
    }

    public function dataValidation()
    {
        yield 'fileがnull' => [fn () => ['file' => null], 'file'];
        yield 'fileがファイル以外' => [fn () => ['file' => 'test.zip'], 'file'];
        yield '成功' => [fn () => ['file' => UploadedFile::fake()->create('test.zip', 1, 'application/zip')], null];
        yield '画像のみで画像以外' => [fn () => ['only_image' => 1, 'file' => UploadedFile::fake()->create('test.zip', 1, 'application/zip')], 'file'];
        yield '画像のみで画像' => [fn () => ['only_image' => 1, 'file' => UploadedFile::fake()->image('test.png', 1)], null];
    }

    /**
     * @dataProvider dataValidation
     */
    public function testStore(Closure $data, ?string $error_field)
    {
        $url = route('api.v2.attachments.store');

        $this->actingAs($this->user);

        $data = Closure::bind($data, $this)();

        $res = $this->postJson($url, $data);
        if (is_null($error_field)) {
            $res->assertOK();
        } else {
            $res->assertJsonValidationErrors($error_field);
        }
    }

    public function testDestroy()
    {
        $user = User::factory()->create();

        $file = $this->createFromFile(UploadedFile::fake()->image('file.png', 1), $user->id);
        $url = route('api.v2.attachments.destroy', $file);
        $res = $this->deleteJson($url);
        $res->assertUnauthorized();

        $this->actingAs($user);

        $other_user = User::factory()->create();
        $other_file = $this->createFromFile(UploadedFile::fake()->image('file.png', 1), $other_user->id);
        $url = route('api.v2.attachments.destroy', $other_file);
        $res = $this->deleteJson($url);
        $res->assertStatus(403);
        $this->assertDatabaseHas('attachments', [
            'id' => $file->id,
        ]);
        $this->assertDatabaseHas('attachments', [
            'id' => $other_file->id,
        ]);

        $url = route('api.v2.attachments.destroy', $file);
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
