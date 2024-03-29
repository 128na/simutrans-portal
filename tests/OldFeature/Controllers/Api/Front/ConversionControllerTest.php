<?php

declare(strict_types=1);

namespace Tests\OldFeature\Controllers\Api\Front;

use Tests\ArticleTestCase;

class ConversionControllerTest extends ArticleTestCase
{
    public function testConversion(): void
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

        $url = '/api/conversion/'.$article->id;
        $response = $this->post($url);
        $response->assertOk();

        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '1', 'period' => $dayly, 'count' => 1]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '2', 'period' => $monthly, 'count' => 1]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '3', 'period' => $yearly, 'count' => 1]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $article->id, 'type' => '4', 'period' => $total, 'count' => 1]);
    }

    public function testShown(): void
    {
        $article = $this->article;

        $dayly = now()->format('Ymd');
        $monthly = now()->format('Ym');
        $yearly = now()->format('Y');
        $total = 'total';

        $this->assertDatabaseMissing('view_counts', ['article_id' => $article->id, 'type' => '1', 'period' => $dayly]);
        $this->assertDatabaseMissing('view_counts', ['article_id' => $article->id, 'type' => '2', 'period' => $monthly]);
        $this->assertDatabaseMissing('view_counts', ['article_id' => $article->id, 'type' => '3', 'period' => $yearly]);
        $this->assertDatabaseMissing('view_counts', ['article_id' => $article->id, 'type' => '4', 'period' => $total]);

        $url = '/api/shown/'.$article->id;
        $response = $this->post($url);
        $response->assertOk();

        $this->assertDatabaseHas('view_counts', ['article_id' => $article->id, 'type' => '1', 'period' => $dayly, 'count' => 1]);
        $this->assertDatabaseHas('view_counts', ['article_id' => $article->id, 'type' => '2', 'period' => $monthly, 'count' => 1]);
        $this->assertDatabaseHas('view_counts', ['article_id' => $article->id, 'type' => '3', 'period' => $yearly, 'count' => 1]);
        $this->assertDatabaseHas('view_counts', ['article_id' => $article->id, 'type' => '4', 'period' => $total, 'count' => 1]);
    }
}
