<?php

namespace Tests\Feature\Front;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarkdownEscapeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function testContent()
    {
        $user = User::factory()->create();

        $markdown = '# hoge';
        $data = [
            'user_id' => $user->id,
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
     * @see https://github.com/cebe/markdown#security-considerations-
     */
    public function testEscape()
    {
        $user = User::factory()->create();

        $keyword = 'keyword' . now()->format('YmdHis');
        $markdown = "# hogehoge";
        $markdown .= "<script>alert('$keyword');</script>";
        $markdown .= "<iframe src='$keyword'></iframe>";
        $markdown .= "<form id='$keyword'></form>";
        $markdown .= "<input id='$keyword'>";
        $markdown .= "<select id='$keyword'></select>";
        $markdown .= "<button id='$keyword'>Button</button>";

        $data = [
            'user_id' => $user->id,
            'post_type' => 'markdown',
            'status' => 'publish',
            'contents' => ['markdown' => $markdown],
        ];
        $article = Article::factory()->create($data);

        $url = route('articles.show', $article->slug);
        $res = $this->get($url);
        $res->assertDontSee($keyword, false);
    }
}
