<?php

namespace Tests\Feature\Controllers\Front\ArticleController\Show;

use App\Models\Article;
use App\Models\Category;
use Tests\TestCase;

class MarkdownTest extends TestCase
{
    private Article $article;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article = Article::factory()->publish()->markdown()->create([
            'user_id' => $this->user->id,
            'contents' => ['markdown' => '# Hoge'],
        ]);
        $this->category = Category::inRandomOrder()->first();
        $this->article->categories()->save($this->category);
    }

    public function test()
    {
        $url = route('articles.show', $this->article->slug);

        $res = $this->get($url);
        $res->assertOk();

        // タイトル
        $res->assertSeeText($this->article->title);
        // マークダウン
        $res->assertSeeText('Hoge');

        // カテゴリ
        $res->assertSeeText(__("category.{$this->category->type}.{$this->category->slug}"));
    }

    /**
     * @dataProvider dataEscape
     *
     * @see https://github.com/cebe/markdown#security-considerations-
     */
    public function testEscape($markdown)
    {
        $this->article->update([
            'contents' => ['markdown' => '# hogehoge'.$markdown],
        ]);

        $url = route('articles.show', $this->article->slug);
        $res = $this->get($url);
        $res->assertDontSee('should_escape', false);
    }

    public function dataEscape()
    {
        yield 'script' => [
            "<script>alert('should_escape');</script>",
        ];
        yield 'iframe' => [
            "<iframe src='should_escape'></iframe>",
        ];
        yield 'form' => [
            "<form id='should_escape'></form>",
        ];
        yield 'select' => [
            "<select id='should_escape'></select>",
        ];
        yield 'button' => [
            "<button id='should_escape'>Button</button>",
        ];
    }
}
