<?php

namespace Tests\Feature\Controllers\Front\ArticleController\Show;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Contents\PageContent;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PageTest extends TestCase
{
    private Attachment $attachment;
    private Article $article;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article = Article::factory()->publish()->page()->create([
            'user_id' => $this->user->id,
        ]);
        $this->attachment = Attachment::factory()->image()->create([
            'user_id' => $this->user->id,
            'attachmentable_type' => Article::class,
            'attachmentable_id' => $this->article->id,
        ]);
        $this->article->update(['contents' => ['sections' => [
                ['type' => 'caption', 'caption' => 'Caption'],
                ['type' => 'text', 'text' => 'Text'],
                ['type' => 'url', 'url' => 'http://example.com'],
                ['type' => 'image', 'id' => $this->attachment->id],
        ]]]);

        $this->category = Category::inRandomOrder()->first();
        $this->article->categories()->save($this->category);
    }

    public function test()
    {
        $url = route('articles.show', $this->article->slug);

        $res = $this->get($url);
        $res->assertOk();
        /**
         * @var PageContent
         */
        $contents = $this->article->contents;

        // タイトル
        $res->assertSeeText($this->article->title);
        // セクション
        $res->assertSeeText('Caption');
        $res->assertSeeText('Text');
        $res->assertSee(Storage::disk('public')->url($this->attachment->path));
        $res->assertSee('http://example.com');

        // カテゴリ
        $res->assertSeeText(__("category.{$this->category->type}.{$this->category->slug}"));
    }
}
