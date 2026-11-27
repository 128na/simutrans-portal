<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\AttachmentController;

use App\Http\Resources\Mypage\AttachmentEdit;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\TestCase;

class IndexTest extends TestCase
{
    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_未ログイン(): void
    {
        $url = '/mypage/attachments';

        $testResponse = $this->get($url);
        $testResponse->assertRedirect('/login');
    }

    public function test_ログイン時にアタッチメント一覧ページを表示(): void
    {
        Attachment::factory()->for($this->user)->count(3)->create();

        $url = '/mypage/attachments';

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertStatus(200);
    }

    public function test_元画像のurlは画像の添付だけに出す(): void
    {
        $disk = Storage::fake('public');
        $imagePath = (string) UploadedFile::fake()->image('photo.png')->store('user/1', 'public');
        $disk->put('user/1/addon.txt', 'addon');
        $image = Attachment::factory()->for($this->user)->create(['path' => $imagePath]);
        $file = Attachment::factory()->for($this->user)->create(['path' => 'user/1/addon.txt']);

        $attachments = collect(AttachmentEdit::collection([$image, $file])->resolve())->keyBy('id');

        $this->assertSame($disk->url($imagePath), $attachments[$image->id]['url']);
        $this->assertArrayNotHasKey('url', $attachments[$file->id]);
    }

    public function test_アタッチメントがない時も正常に表示(): void
    {
        $url = '/mypage/attachments';

        $this->actingAs($this->user);

        $testResponse = $this->get($url);
        $testResponse->assertStatus(200);
    }
}
