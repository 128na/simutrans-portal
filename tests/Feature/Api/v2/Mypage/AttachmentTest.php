<?php

namespace Tests\Feature\Api\v2\Mypage;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AttachmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function testIndex()
    {
        $user = factory(User::class)->create();
        $file = Attachment::createFromFile(UploadedFile::fake()->create('file.zip', 1), $user->id);

        $url = route('api.v2.attachments.index');

        $res = $this->getJson($url);
        $res->assertUnauthorized();

        $this->actingAs($user);

        $res = $this->getJson($url);
        $res->assertOK();
    }

    public function testFormatImage()
    {
        $user = factory(User::class)->create();
        $file = Attachment::createFromFile(UploadedFile::fake()->image('file.png', 1), $user->id);

        $url = route('api.v2.attachments.index');
        $this->actingAs($user);

        $res = $this->getJson($url);
        $res->assertOK();
        $res->assertExactJson(['data' => [
            [
                'id' => $file->id,
                'attachmentable_type' => "",
                'attachmentable_id' => null,
                'type' => 'image',
                'original_name' => $file->original_name,
                'thumbnail' => $file->thumbnail,
                'url' => $file->url,
            ],
        ]]);
    }

    // mime_typeが働かないのでzipなどは判定できない
    public function testFormatOther()
    {
        $user = factory(User::class)->create();
        $file = Attachment::createFromFile(UploadedFile::fake()->create('file.zip', 1), $user->id);

        $url = route('api.v2.attachments.index');
        $this->actingAs($user);

        $res = $this->getJson($url);
        $res->assertOK();
        $res->assertExactJson(['data' => [
            [
                'id' => $file->id,
                'attachmentable_type' => "",
                'attachmentable_id' => null,
                'type' => 'file',
                'original_name' => $file->original_name,
                'thumbnail' => asset('storage/' . config('attachment.thumbnail-file')),
                'url' => $file->url,
            ],
        ]]);
    }

    public function testStore()
    {
        $user = factory(User::class)->create();

        $url = route('api.v2.attachments.store');

        $res = $this->postJson($url);
        $res->assertUnauthorized();

        $data = [
            'file' => UploadedFile::fake()->create('file.zip', 1),
            'only_image' => false,
        ];

        $this->actingAs($user);

        $res = $this->postJson($url, array_merge($data, ['file' => null]));
        $res->assertJsonValidationErrors(['file']);
        $res = $this->postJson($url, array_merge($data, ['file' => 'not_file']));
        $res->assertJsonValidationErrors(['file']);

        $res = $this->postJson($url, $data);
        $res->assertOK();

        $file = Attachment::first();
        $res->assertExactJson(['data' => [
            [
                'id' => $file->id,
                'attachmentable_type' => "",
                'attachmentable_id' => null,
                'type' => 'file',
                'original_name' => $file->original_name,
                'thumbnail' => $file->thumbnail,
                'url' => $file->url,
            ],
        ]]);
    }

    public function testStoreImage()
    {
        $user = factory(User::class)->create();

        $url = route('api.v2.attachments.store');

        $res = $this->postJson($url);
        $res->assertUnauthorized();

        $data = [
            'file' => UploadedFile::fake()->image('file.png', 1),
            'only_image' => true,
        ];

        $this->actingAs($user);

        $res = $this->postJson($url, array_merge($data, ['file' => null]));
        $res->assertJsonValidationErrors(['file']);
        $res = $this->postJson($url, array_merge($data, ['file' => 'not_file']));
        $res->assertJsonValidationErrors(['file']);
        $res = $this->postJson($url, array_merge($data, ['file' => UploadedFile::fake()->create('file.zip', 1)]));
        $res->assertJsonValidationErrors(['file']);
        $res = $this->postJson($url, $data);
        $res->assertOK();

        $file = Attachment::first();
        $res->assertExactJson(['data' => [
            [
                'id' => $file->id,
                'attachmentable_type' => "",
                'attachmentable_id' => null,
                'type' => 'image',
                'original_name' => $file->original_name,
                'thumbnail' => $file->thumbnail,
                'url' => $file->url,
            ],
        ]]);
    }

    public function testDestroy()
    {
        $user = factory(User::class)->create();

        $file = Attachment::createFromFile(UploadedFile::fake()->image('file.png', 1), $user->id);
        $url = route('api.v2.attachments.destroy', $file);
        $res = $this->deleteJson($url);
        $res->assertUnauthorized();

        $this->actingAs($user);

        $other_user = factory(User::class)->create();
        $other_file = Attachment::createFromFile(UploadedFile::fake()->image('file.png', 1), $other_user->id);
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
        $this->assertDatabaseMissing('attachments', [
            'id' => $file->id,
        ]);
    }
}
