<?php

namespace Tests\Feature\Http\Controllers\Front\ArticleController;

use App\Models\User;
use Closure;
use Tests\ArticleTestCase;

class ShowTest extends ArticleTestCase
{
    /**
     *  @dataProvider dataShow
     * */
    public function testShow(Closure $fn)
    {
        $fn = Closure::bind($fn, $this);
        $article = $fn();
        $response = $this->get('/articles/'.$article->slug);
        $response->assertOk();
    }

    public function dataShow()
    {
        yield 'アドオン投稿' => [fn () => $this->createAddonPost()];
        yield 'アドオン紹介' => [fn () => $this->createAddonIntroduction()];
        yield '一般記事' => [fn () => $this->createPage()];
        yield 'Markdown形式の記事' => [fn () => $this->createMarkdown()];
        yield 'お知らせ' => [fn () => $this->createAnnounce()];
        yield 'Markdown形式のお知らせ' => [fn () => $this->createMarkdownAnnounce()];
    }

    /**
     *  存在しない記事は404となること.
     * */
    public function testMissingArticle()
    {
        $article = $this->createAddonIntroduction();
        $response = $this->get('/articles/'.$article->slug.'missing');
        $response->assertNotFound();
    }

    /**
     *  @dataProvider dataStatus
     * */
    public function testStatus(string $status, bool $should_see)
    {
        $this->article->fill(['status' => $status])->save();
        $response = $this->get('/articles/'.$this->article->slug);

        if ($should_see) {
            $response->assertOk();
        } else {
            $response->assertNotFound();
        }
    }

    public function testUserSoftDeleted()
    {
        $response = $this->get('/articles/'.$this->article->slug);
        $response->assertOk();

        $this->user->delete();

        $response = $this->get('/articles/'.$this->article->slug);
        $response->assertNotFound();
    }

    public function testArticleSoftDeleted()
    {
        $response = $this->get('/articles/'.$this->article->slug);
        $response->assertOk();

        $this->article->delete();

        $response = $this->get('/articles/'.$this->article->slug);
        $response->assertNotFound();
    }

    /**
     * @dataProvider dataCount
     * */
    public function testViewCount(Closure $fn, ?int $expected_count)
    {
        $dayly = now()->format('Ymd');
        $monthly = now()->format('Ym');
        $yearly = now()->format('Y');
        $total = 'total';

        $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article->id, 'type' => '1', 'period' => $dayly]);
        $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article->id, 'type' => '2', 'period' => $monthly]);
        $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article->id, 'type' => '3', 'period' => $yearly]);
        $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article->id, 'type' => '4', 'period' => $total]);

        $fn = Closure::bind($fn, $this);
        $user = $fn();
        if ($user) {
            $this->actingAs($user);
        }
        $response = $this->get('articles/'.$this->article->slug);
        $response->assertOk();

        if (is_null($expected_count)) {
            $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article->id, 'type' => '1', 'period' => $dayly]);
            $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article->id, 'type' => '2', 'period' => $monthly]);
            $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article->id, 'type' => '3', 'period' => $yearly]);
            $this->assertDatabaseMissing('view_counts', ['article_id' => $this->article->id, 'type' => '4', 'period' => $total]);
        } else {
            $this->assertDatabaseHas('view_counts', ['article_id' => $this->article->id, 'type' => '1', 'period' => $dayly, 'count' => $expected_count]);
            $this->assertDatabaseHas('view_counts', ['article_id' => $this->article->id, 'type' => '2', 'period' => $monthly, 'count' => $expected_count]);
            $this->assertDatabaseHas('view_counts', ['article_id' => $this->article->id, 'type' => '3', 'period' => $yearly, 'count' => $expected_count]);
            $this->assertDatabaseHas('view_counts', ['article_id' => $this->article->id, 'type' => '4', 'period' => $total, 'count' => $expected_count]);
        }
    }

    /**
     * @dataProvider dataCount
     * */
    public function testConversionCountAddonPost(Closure $fn, ?int $expected_count)
    {
        $article = $this->createAddonPost();

        $dayly = now()->format('Ymd');
        $monthly = now()->format('Ym');
        $yearly = now()->format('Y');
        $total = 'total';

        $this->assertDatabaseMissing('conversion_counts', ['article_id' => $article->id, 'type' => '1', 'period' => $dayly]);
        $this->assertDatabaseMissing('conversion_counts', ['article_id' => $article->id, 'type' => '2', 'period' => $monthly]);
        $this->assertDatabaseMissing('conversion_counts', ['article_id' => $article->id, 'type' => '3', 'period' => $yearly]);
        $this->assertDatabaseMissing('conversion_counts', ['article_id' => $article->id, 'type' => '4', 'period' => $total]);

        $fn = Closure::bind($fn, $this);
        $user = $fn();
        if ($user) {
            $this->actingAs($user);
        }
        $response = $this->get('articles/'.$article->slug.'/download');
        $response->assertOk();

        if (is_null($expected_count)) {
            $this->assertDatabaseMissing('conversion_counts', ['article_id' => $article->id, 'type' => '1', 'period' => $dayly]);
            $this->assertDatabaseMissing('conversion_counts', ['article_id' => $article->id, 'type' => '2', 'period' => $monthly]);
            $this->assertDatabaseMissing('conversion_counts', ['article_id' => $article->id, 'type' => '3', 'period' => $yearly]);
            $this->assertDatabaseMissing('conversion_counts', ['article_id' => $article->id, 'type' => '4', 'period' => $total]);
        } else {
            $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '1', 'period' => $dayly, 'count' => $expected_count]);
            $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '2', 'period' => $monthly, 'count' => $expected_count]);
            $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '3', 'period' => $yearly, 'count' => $expected_count]);
            $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '4', 'period' => $total, 'count' => $expected_count]);
        }
    }

    public function dataCount()
    {
        yield '未ログイン' => [fn () => null, 1];
        yield '記事の投稿者' => [fn () => $this->user, null];
        yield '他のユーザー' => [fn () => User::factory()->create(), 1];
    }
}
