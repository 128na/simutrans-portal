<?php

namespace Tests\Feature\Http\Controllers\Api\v1;

use App\Models\User;
use Closure;
use Tests\ArticleTestCase;

class ConversionControllerTest extends ArticleTestCase
{
    /**
     * @dataProvider dataCount
     * */
    public function test(Closure $fn, ?int $expected_count)
    {
        $article = $this->article;

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
        $url = route('api.v1.click', [$article->slug]);
        $response = $this->post($url);
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
