<?php

namespace Tests\Feature\Controllers\Front\ArticleController;

use App\Models\Article;
use App\Models\Category;
use Tests\TestCase;

class AnnouncesTest extends TestCase
{
    private Article $article1;
    private Article $article2;
    private Article $article3;
    private Article $article4;
    private Article $article5;
    private Article $article6;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article1 = Article::factory()->publish()->addonIntroduction()->create();
        $this->article2 = Article::factory()->publish()->addonPost()->create();
        $this->article3 = Article::factory()->publish()->page()->create();
        $this->article4 = Article::factory()->publish()->markdown()->create();

        $this->article5 = Article::factory()->publish()->page()->create();
        $this->article6 = Article::factory()->publish()->markdown()->create();
        $announce = Category::page()->slug('announce')->firstOrFail();
        $this->article5->categories()->save($announce);
        $this->article6->categories()->save($announce);
    }

    public function test()
    {
        $url = route('announces.index');
        $res = $this->get($url);
        $res->assertOk();
        $res->assertDontSeeText($this->article1->title);
        $res->assertDontSeeText($this->article2->title);
        $res->assertDontSeeText($this->article3->title);
        $res->assertDontSeeText($this->article4->title);
        $res->assertSeeText($this->article5->title);
        $res->assertSeeText($this->article6->title);
    }

    public function test_非公開()
    {
        $this->article5->update(['status' => 'private']);
        $this->article6->update(['status' => 'private']);

        $url = route('announces.index');
        $res = $this->get($url);
        $res->assertOk();
        $res->assertDontSeeText($this->article5->title);
        $res->assertDontSeeText($this->article6->title);
    }
}
