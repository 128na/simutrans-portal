<?php

namespace Tests\Feature\Controllers\Front\ArticleController;

use App\Models\Article;
use Tests\TestCase;

class SearchTest extends TestCase
{
    private Article $article1;
    private Article $article2;
    private Article $article3;
    private Article $article4;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article1 = Article::factory()->publish()->addonIntroduction()->create(['title' => 'key1']);
        $this->article2 = Article::factory()->publish()->addonPost()->create(['title' => 'key2']);
        $this->article3 = Article::factory()->publish()->page()->create(['title' => 'key3']);
        $this->article4 = Article::factory()->publish()->markdown()->create(['title' => 'key4']);
    }

    public function test()
    {
        $url = route('search', ['word' => 'key']);
        $res = $this->get($url);
        $res->assertOk();
        $res->assertSeeText($this->article1->title);
        $res->assertSeeText($this->article2->title);
        $res->assertSeeText($this->article3->title);
        $res->assertSeeText($this->article4->title);
    }

    public function test_非公開()
    {
        $this->article1->update(['status' => 'private']);

        $url = route('search', ['word' => 'key1']);
        $res = $this->get($url);
        $res->assertOk();
        $res->assertSeeText('記事がありません');
    }
}
