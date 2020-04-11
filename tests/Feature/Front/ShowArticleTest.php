<?php

namespace Tests\Feature\Front;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowArticleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    /**
     *  アドオン投稿が表示されること
     * */
    public function testShowAddonPost()
    {
        $article = static::createAddonPost();
        $response = $this->get('/articles/' . $article->slug);
        $response->assertOk();
    }

    /**
     *  アドオン紹介が表示されること
     * */
    public function testShowAddonIntroduction()
    {
        $article = static::createAddonIntroduction();
        $response = $this->get('/articles/' . $article->slug);
        $response->assertOk();
    }

    /**
     *  一般記事が表示されること
     * */
    public function testShowPage()
    {
        $article = static::createPage();
        $response = $this->get('/articles/' . $article->slug);
        $response->assertOk();

    }

    /**
     *  Markdown形式の記事が表示されること
     * */
    public function testShowMarkdown()
    {
        $article = static::createMarkdown();
        $response = $this->get('/articles/' . $article->slug);
        $response->assertOk();

    }

    /**
     *  お知らせが表示されること
     * */
    public function testShowAnnounce()
    {
        $article = static::createAnnounce();
        $response = $this->get('/articles/' . $article->slug);
        $response->assertOk();
    }

    /**
     *  Markdown形式のお知らせが表示されること
     * */
    public function testShowMarkdownAnnounce()
    {
        $article = static::createMarkdownAnnounce();
        $response = $this->get('/articles/' . $article->slug);
        $response->assertOk();
    }

    /**
     *  存在しない記事は404となること
     * */
    public function testMissingArticle()
    {
        $article = static::createAddonIntroduction();
        $response = $this->get('/articles/' . $article->slug . 'missing');
        $response->assertNotFound();
    }

    /**
     *  非公開の記事は404となること
     * */
    public function testUnpublishArticle()
    {
        $user = factory(User::class)->create();
        $article = factory(Article::class)->create(['user_id' => $user->id, 'status' => 'publish']);
        $response = $this->get('/articles/' . $article->slug);
        $response->assertOk();

        $article = factory(Article::class)->create(['user_id' => $user->id, 'status' => 'draft']);
        $response = $this->get('/articles/' . $article->slug);
        $response->assertNotFound();

        $article = factory(Article::class)->create(['user_id' => $user->id, 'status' => 'private']);
        $response = $this->get('/articles/' . $article->slug);
        $response->assertNotFound();

        $article = factory(Article::class)->create(['user_id' => $user->id, 'status' => 'trash']);
        $response = $this->get('/articles/' . $article->slug);
        $response->assertNotFound();
    }

    /**
     *  未ログインユーザーが記事を表示したときview_countsが日次、月次、年次、合計の値が+1されること
     *  ログインユーザーが自分以外の記事を表示したときview_countsが日次、月次、年次、合計の値が+1されること
     *  ログインユーザーが自分の記事を表示したときview_countsが日次、月次、年次、合計の値が更新されないこと
     * */
    public function testViewCount()
    {
        $user = factory(User::class)->create();
        $article = factory(Article::class)->create(['user_id' => $user->id, 'status' => 'publish']);

        $dayly = now()->format('Ymd');
        $monthly = now()->format('Ym');
        $yearly = now()->format('Y');
        $total = 'total';

        $this->assertDatabaseMissing('view_counts', ['article_id' => $article->id, 'type' => '1', 'period' => $dayly]);
        $this->assertDatabaseMissing('view_counts', ['article_id' => $article->id, 'type' => '2', 'period' => $monthly]);
        $this->assertDatabaseMissing('view_counts', ['article_id' => $article->id, 'type' => '3', 'period' => $yearly]);
        $this->assertDatabaseMissing('view_counts', ['article_id' => $article->id, 'type' => '4', 'period' => $total]);

        $response = $this->get('articles/' . $article->slug);
        $response->assertOk();

        $this->assertDatabaseHas('view_counts', ['article_id' => $article->id, 'type' => '1', 'period' => $dayly, 'count' => 1]);
        $this->assertDatabaseHas('view_counts', ['article_id' => $article->id, 'type' => '2', 'period' => $monthly, 'count' => 1]);
        $this->assertDatabaseHas('view_counts', ['article_id' => $article->id, 'type' => '3', 'period' => $yearly, 'count' => 1]);
        $this->assertDatabaseHas('view_counts', ['article_id' => $article->id, 'type' => '4', 'period' => $total, 'count' => 1]);

        $other_user = factory(User::class)->create();
        $response = $this->actingAs($other_user)->get('articles/' . $article->slug);
        $response->assertOk();

        $this->assertDatabaseHas('view_counts', ['article_id' => $article->id, 'type' => '1', 'period' => $dayly, 'count' => 2]);
        $this->assertDatabaseHas('view_counts', ['article_id' => $article->id, 'type' => '2', 'period' => $monthly, 'count' => 2]);
        $this->assertDatabaseHas('view_counts', ['article_id' => $article->id, 'type' => '3', 'period' => $yearly, 'count' => 2]);
        $this->assertDatabaseHas('view_counts', ['article_id' => $article->id, 'type' => '4', 'period' => $total, 'count' => 2]);

        $response = $this->actingAs($user)->get('articles/' . $article->slug);
        $response->assertOk();

        $this->assertDatabaseHas('view_counts', ['article_id' => $article->id, 'type' => '1', 'period' => $dayly, 'count' => 2]);
        $this->assertDatabaseHas('view_counts', ['article_id' => $article->id, 'type' => '2', 'period' => $monthly, 'count' => 2]);
        $this->assertDatabaseHas('view_counts', ['article_id' => $article->id, 'type' => '3', 'period' => $yearly, 'count' => 2]);
        $this->assertDatabaseHas('view_counts', ['article_id' => $article->id, 'type' => '4', 'period' => $total, 'count' => 2]);
    }

    /**
     *  未ログインユーザーがアドオン紹介記事のDL先リンクをクリックしたときconversion_countsが日次、月次、年次、合計の値が+1されること
     *  ログインユーザーが自分以外のアドオン紹介記事のDL先リンクをクリックしたときconversion_countsが日次、月次、年次、合計の値が+1されること
     *  ログインユーザーが自分のアドオン紹介記事のDL先リンクをクリックしたときconversion_countsが日次、月次、年次、合計の値が更新されないこと
     * */
    public function testConversionCountAddonIntroduction()
    {
        $user = factory(User::class)->create();
        $article = static::createAddonIntroduction($user);

        $dayly = now()->format('Ymd');
        $monthly = now()->format('Ym');
        $yearly = now()->format('Y');
        $total = 'total';

        $this->assertDatabaseMissing('conversion_counts', ['article_id' => $article->id, 'type' => '1', 'period' => $dayly]);
        $this->assertDatabaseMissing('conversion_counts', ['article_id' => $article->id, 'type' => '2', 'period' => $monthly]);
        $this->assertDatabaseMissing('conversion_counts', ['article_id' => $article->id, 'type' => '3', 'period' => $yearly]);
        $this->assertDatabaseMissing('conversion_counts', ['article_id' => $article->id, 'type' => '4', 'period' => $total]);

        $response = $this->post('api/v1/click/' . $article->slug);
        $response->assertOk();

        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '1', 'period' => $dayly, 'count' => 1]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '2', 'period' => $monthly, 'count' => 1]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '3', 'period' => $yearly, 'count' => 1]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '4', 'period' => $total, 'count' => 1]);

        $other_user = factory(User::class)->create();
        $response = $this->actingAs($other_user)->post('api/v1/click/' . $article->slug);
        $response->assertOk();

        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '1', 'period' => $dayly, 'count' => 2]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '2', 'period' => $monthly, 'count' => 2]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '3', 'period' => $yearly, 'count' => 2]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '4', 'period' => $total, 'count' => 2]);

        $response = $this->actingAs($user)->post('api/v1/click/' . $article->slug);
        $response->assertOk();

        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '1', 'period' => $dayly, 'count' => 2]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '2', 'period' => $monthly, 'count' => 2]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '3', 'period' => $yearly, 'count' => 2]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '4', 'period' => $total, 'count' => 2]);
    }

    /**
     *  未ログインユーザーがアドオン紹介記事のDL先リンクをクリックしたときconversion_countsが日次、月次、年次、合計の値が+1されること
     *  ログインユーザーが自分以外のアドオン紹介記事のDL先リンクをクリックしたときconversion_countsが日次、月次、年次、合計の値が+1されること
     *  ログインユーザーが自分のアドオン紹介記事のDL先リンクをクリックしたときconversion_countsが日次、月次、年次、合計の値が更新されないこと
     *
     *  テストでは保存したフィルが取得できず500エラーになる
     *  diskインスタンス経由でファイル取得していないのが原因？
     * */
    public function testConversionCountAddonPost()
    {
        $user = factory(User::class)->create();
        $article = static::createAddonPost($user);

        $dayly = now()->format('Ymd');
        $monthly = now()->format('Ym');
        $yearly = now()->format('Y');
        $total = 'total';

        $this->assertDatabaseMissing('conversion_counts', ['article_id' => $article->id, 'type' => '1', 'period' => $dayly]);
        $this->assertDatabaseMissing('conversion_counts', ['article_id' => $article->id, 'type' => '2', 'period' => $monthly]);
        $this->assertDatabaseMissing('conversion_counts', ['article_id' => $article->id, 'type' => '3', 'period' => $yearly]);
        $this->assertDatabaseMissing('conversion_counts', ['article_id' => $article->id, 'type' => '4', 'period' => $total]);

        $response = $this->get('articles/' . $article->slug . '/download');
        $response->assertOk();

        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '1', 'period' => $dayly, 'count' => 1]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '2', 'period' => $monthly, 'count' => 1]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '3', 'period' => $yearly, 'count' => 1]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '4', 'period' => $total, 'count' => 1]);

        $other_user = factory(User::class)->create();
        $response = $this->actingAs($other_user)->get('articles/' . $article->slug . '/download');
        $response->assertOk();

        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '1', 'period' => $dayly, 'count' => 2]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '2', 'period' => $monthly, 'count' => 2]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '3', 'period' => $yearly, 'count' => 2]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '4', 'period' => $total, 'count' => 2]);

        $response = $this->actingAs($user)->get('articles/' . $article->slug . '/download');
        $response->assertOk();

        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '1', 'period' => $dayly, 'count' => 2]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '2', 'period' => $monthly, 'count' => 2]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '3', 'period' => $yearly, 'count' => 2]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '4', 'period' => $total, 'count' => 2]);
    }

}
