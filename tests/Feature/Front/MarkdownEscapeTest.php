<?php

namespace Tests\Feature\Front;

use App\Models\Article;
use Tests\TestCase;

class MarkdownEscapeTest extends TestCase
{
    public function testContent()
    {
        $markdown = '# hoge';
        $data = [
            'user_id' => $this->user->id,
            'post_type' => 'markdown',
            'status' => 'publish',
            'contents' => ['markdown' => $markdown],
        ];
        $article = Article::factory()->create($data);

        $url = route('articles.show', $article->slug);
        $res = $this->get($url);
        $res->assertSee('<h1>hoge</h1>', false);
    }

    /**
     * @dataProvider dataEscape
     *
     * @see https://github.com/cebe/markdown#security-considerations-
     */
    public function testEscape($markdown)
    {
        $markdown = '# hogehoge'.$markdown;

        $data = [
            'user_id' => $this->user->id,
            'post_type' => 'markdown',
            'status' => 'publish',
            'contents' => ['markdown' => $markdown],
        ];
        $article = Article::factory()->create($data);

        $url = route('articles.show', $article->slug);
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
