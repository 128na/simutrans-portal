<?php

namespace Tests\Feature\Controllers\Front\ArticleController\Show;

use App\Models\Article;
use App\Models\Category;
use App\Models\Contents\AddonIntroductionContent;
use App\Models\Tag;
use Tests\TestCase;

class AddonIntroductionTest extends TestCase
{
    private Article $article;
    private Article $article2;
    private Category $category;
    private Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article = Article::factory()->publish()->addonIntroduction()->create(['user_id' => $this->user->id]);
        $this->category = Category::inRandomOrder()->first();
        $this->article->categories()->save($this->category);
        $this->tag = Tag::factory()->create();
        $this->article->tags()->save($this->tag);

        $this->article2 = Article::factory()->publish()->addonIntroduction()->create(['user_id' => $this->user->id]);
        tap($this->article2->contents, function (AddonIntroductionContent $content) {
            $content->agreement = false;
        });
        $this->article2->save();
    }

    public function test()
    {
        $this->markTestSkipped('ブラウザテストに移行する');
        $url = route('articles.show', $this->article->slug);

        $res = $this->get($url);
        $res->assertOk();
        /**
         * @var AddonIntroductionContent
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
        // 掲載許可
        $res->assertSeeText('この記事は作者の許可を得てまたは作者自身により掲載しています。');
        // 掲載先URL
        $res->assertSee($contents->link);
    }

    public function test未同意()
    {
        $this->markTestSkipped('ブラウザテストに移行する');
        $url = route('articles.show', $this->article2->slug);

        $res = $this->get($url);
        $res->assertOk();
        /**
         * @var AddonIntroductionContent
         */
        $contents = $this->article2->contents;

        // タイトル
        $res->assertSeeText($this->article2->title);
        // 作者 / 投稿者
        $res->assertSeeText($contents->author);
        $res->assertSeeText($this->user->name);
        // 説明
        $res->assertSeeText($contents->description);
        // ライセンス
        $res->assertSeeText($contents->license);
        // 謝辞
        $res->assertSeeText($contents->thanks);
        // 掲載許可
        $res->assertDontSeeText('この記事は作者の許可を得てまたは作者自身により掲載しています。');
        // 掲載先URL
        $res->assertSee($contents->link);
    }
}
