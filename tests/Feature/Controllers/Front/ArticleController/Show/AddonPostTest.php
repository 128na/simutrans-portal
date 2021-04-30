<?php

namespace Tests\Feature\Controllers\Front\ArticleController\Show;

use App\Models\Article;
use App\Models\Category;
use App\Models\Contents\AddonPostContent;
use App\Models\Tag;
use Tests\TestCase;

class AddonPostTest extends TestCase
{
    private Article $article;
    private Category $category;
    private Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article = Article::factory()->publish()->addonPost()->create(['user_id' => $this->user->id]);
        $this->category = Category::inRandomOrder()->first();
        $this->article->categories()->save($this->category);
        $this->tag = Tag::factory()->create();
        $this->article->tags()->save($this->tag);
    }

    public function test()
    {
        $url = route('articles.show', $this->article->slug);

        $res = $this->get($url);
        $res->assertOk();
        /**
         * @var AddonPostContent
         */
        $contents = $this->article->contents;

        // タイトル
        $res->assertSeeText($this->article->title);
        // 作者 / 投稿者
        $res->assertSeeText($contents->author);
        $res->assertSeeText($this->user->name);
        // カテゴリ
        $res->assertSeeText(__("category.{$this->category->type}.{$this->category->slug}"));
        // タグ
        $res->assertSeeText($this->tag->name);
        // 説明
        $res->assertSeeText($contents->description);
        // ライセンス
        $res->assertSeeText($contents->license);
        // 謝辞
        $res->assertSeeText($contents->thanks);
    }
}
