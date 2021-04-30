<?php

namespace Tests\Feature\Controllers\Front\ArticleController\Show;

use App\Models\Article;
use App\Models\Category;
use App\Models\Contents\MarkdownContent;
use Tests\TestCase;

class MarkdownTest extends TestCase
{
    private Article $article;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article = Article::factory()->publish()->markdown()->create(['user_id' => $this->user->id]);
        $this->category = Category::inRandomOrder()->first();
        $this->article->categories()->save($this->category);
    }

    public function test()
    {
        $url = route('articles.show', $this->article->slug);

        $res = $this->get($url);
        $res->assertOk();
        /**
         * @var MarkdownContent
         */
        $contents = $this->article->contents;

        // タイトル
        $res->assertSeeText($this->article->title);
        $this->markTestIncomplete();
    }
}
