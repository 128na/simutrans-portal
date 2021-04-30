<?php

namespace Tests\Feature\Controllers\Front\ArticleController;

use App\Models\Article;
use Tests\TestCase;

class UserTest extends TestCase
{
    private Article $article1;
    private Article $article2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article1 = Article::factory()->publish()->addonIntroduction()->create(['user_id' => $this->user->id]);
        $this->article2 = Article::factory()->publish()->addonIntroduction()->create();
    }

    public function test()
    {
        $url = route('user', $this->user);
        $res = $this->get($url);
        $res->assertOk();
        $res->assertSeeText($this->article1->title);
        $res->assertDontSeeText($this->article2->title);
    }

    public function test_非公開()
    {
        $this->article1->update(['status' => 'private']);

        $url = route('user', $this->user);
        $res = $this->get($url);
        $res->assertOk();
        $res->assertDontSeeText($this->article1->title);
    }
}
