<?php

namespace Tests\Feature\Controllers\Front\ArticleController;

use App\Models\Article;
use Tests\TestCase;

class ShowTest extends TestCase
{
    private Article $article1;
    private Article $article2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article1 = Article::factory()->publish()->create(['user_id' => $this->user->id]);
        $this->article2 = Article::factory()->draft()->create();
    }

    public function test()
    {
        $url = route('articles.show', $this->article1->slug);

        $res = $this->get($url);
        $res->assertOk();
        $res->assertSeeText($this->article1->title);
    }

    public function test非公開()
    {
        $url = route('articles.show', $this->article2->slug);

        $res = $this->get($url);
        $res->assertNotFound();
    }

    public function test404()
    {
        $url = route('articles.show', 'aaa');

        $res = $this->get($url);
        $res->assertNotFound();
    }

    public function testユーザー削除済み()
    {
        $this->user->delete();
        $url = route('articles.show', 'aaa');

        $res = $this->get($url);
        $res->assertNotFound();
    }

    public function test記事削除済み()
    {
        $this->article1->delete();
        $url = route('articles.show', $this->article1->slug);

        $res = $this->get($url);
        $res->assertNotFound();
    }

    public function testPV()
    {
        $dayly = now()->format('Ymd');
        $monthly = now()->format('Ym');
        $yearly = now()->format('Y');
        $total = 'total';

        $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article1->id, 'type' => '1', 'period' => $dayly]);
        $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article1->id, 'type' => '2', 'period' => $monthly]);
        $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article1->id, 'type' => '3', 'period' => $yearly]);
        $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article1->id, 'type' => '4', 'period' => $total]);

        $url = route('articles.show', $this->article1->slug);
        $res = $this->get($url);
        $res->assertOk();

        $this->assertDatabaseHas('view_counts', ['article_id' => $this->article1->id, 'type' => '1', 'period' => $dayly, 'count' => 1]);
        $this->assertDatabaseHas('view_counts', ['article_id' => $this->article1->id, 'type' => '2', 'period' => $monthly, 'count' => 1]);
        $this->assertDatabaseHas('view_counts', ['article_id' => $this->article1->id, 'type' => '3', 'period' => $yearly, 'count' => 1]);
        $this->assertDatabaseHas('view_counts', ['article_id' => $this->article1->id, 'type' => '4', 'period' => $total, 'count' => 1]);
    }

    public function testPV投稿者のときはカウントしない()
    {
        $dayly = now()->format('Ymd');
        $monthly = now()->format('Ym');
        $yearly = now()->format('Y');
        $total = 'total';

        $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article1->id, 'type' => '1', 'period' => $dayly]);
        $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article1->id, 'type' => '2', 'period' => $monthly]);
        $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article1->id, 'type' => '3', 'period' => $yearly]);
        $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article1->id, 'type' => '4', 'period' => $total]);

        $this->actingAs($this->user);
        $url = route('articles.show', $this->article1->slug);
        $res = $this->get($url);
        $res->assertOk();

        $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article1->id, 'type' => '1', 'period' => $dayly]);
        $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article1->id, 'type' => '2', 'period' => $monthly]);
        $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article1->id, 'type' => '3', 'period' => $yearly]);
        $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article1->id, 'type' => '4', 'period' => $total]);
    }
}
