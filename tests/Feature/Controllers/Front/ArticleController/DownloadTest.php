<?php

namespace Tests\Feature\Controllers\Front\ArticleController;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Contents\AddonPostContent;
use Illuminate\Filesystem\FilesystemAdapter;
use Storage;
use Tests\TestCase;

class DownloadTest extends TestCase
{
    private FilesystemAdapter $disk;
    private Article $article1;
    private Attachment $attachment;
    private Article $article2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->disk = Storage::disk('public');
        $this->disk->put('dummy.zip', 'dummy');

        $this->article1 = Article::factory()->publish()->addonPost()->create(['user_id' => $this->user->id]);
        $this->attachment = Attachment::factory()->create([
            'user_id' => $this->user->id,
            'attachmentable_type' => Article::class,
            'attachmentable_id' => $this->article1->id,
            'path' => 'dummy.zip',
            'original_name' => 'original.zip',
        ]);
        tap($this->article1->contents, function (AddonPostContent $content) {
            $content->file = $this->attachment->id;
        });
        $this->article1->save();

        $this->article2 = Article::factory()->publish()->addonIntroduction()->create();
    }

    protected function tearDown(): void
    {
        $this->disk->delete('dummy.zip');
        parent::tearDown();
    }

    public function test()
    {
        $url = route('articles.download', $this->article1->slug);
        $res = $this->get($url);
        $res->assertOk();
        $res->assertHeader('content-disposition', 'attachment; filename=original.zip');
    }

    public function test_非公開()
    {
        $this->article1->update(['status' => 'private']);
        $url = route('articles.download', $this->article1->slug);
        $res = $this->get($url);
        $res->assertNotFound();
    }

    public function test_アドオン投稿以外()
    {
        $url = route('articles.download', $this->article2->slug);
        $res = $this->get($url);
        $res->assertNotFound();
    }
}
