<?php

namespace Tests\Feature\Controllers\Front\ArticleController;

use App\Models\Article;
use App\Models\Article\Ranking;
use Tests\TestCase;

class RankingTest extends TestCase
{
    private Article $article1;
    private Article $article2;
    private Article $article3;
    private Article $article4;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article1 = Article::factory()->publish()->addonIntroduction()->create();
        $this->article2 = Article::factory()->publish()->addonPost()->create();
        $this->article3 = Article::factory()->publish()->page()->create();
        $this->article4 = Article::factory()->publish()->markdown()->create();

        Ranking::create(['rank' => 1, 'article_id' => $this->article1->id]);
        Ranking::create(['rank' => 2, 'article_id' => $this->article2->id]);
    }

    public function test()
    {
        $url = route('addons.ranking');
        $res = $this->get($url);
        $res->assertOk();
        $res->assertSeeText($this->article1->title);
        $res->assertSeeText($this->article2->title);
        $res->assertDontSeeText($this->article3->title);
        $res->assertDontSeeText($this->article4->title);
    }

    public function test非公開()
    {
        $this->article1->update(['status' => 'private']);

        $url = route('addons.ranking');
        $res = $this->get($url);
        $res->assertOk();
        $res->assertDontSeeText($this->article1->title);
    }
}
