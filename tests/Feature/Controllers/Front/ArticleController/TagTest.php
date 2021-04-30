<?php

namespace Tests\Feature\Controllers\Front\ArticleController;

use App\Models\Article;
use App\Models\Tag;
use Tests\TestCase;

class TagTest extends TestCase
{
    private Article $article1;
    private Article $article2;
    private Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article1 = Article::factory()->publish()->addonIntroduction()->create();
        $this->article2 = Article::factory()->publish()->addonIntroduction()->create();
        $this->tag = Tag::factory()->create();
        $this->article1->tags()->save($this->tag);
    }

    public function test()
    {
        $url = route('tag', $this->tag);
        $res = $this->get($url);
        $res->assertOk();
        $res->assertSeeText($this->article1->title);
        $res->assertDontSeeText($this->article2->title);
    }

    public function test_非公開()
    {
        $this->article1->update(['status' => 'private']);

        $url = route('tag', $this->tag);
        $res = $this->get($url);
        $res->assertOk();
        $res->assertDontSeeText($this->article1->title);
    }
}
