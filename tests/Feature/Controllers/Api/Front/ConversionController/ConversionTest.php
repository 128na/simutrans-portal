<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Front\ConversionController;

use App\Models\Article;
use Tests\Feature\TestCase;

final class ConversionTest extends TestCase
{
    private Article $article;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->article = Article::factory()->publish()->create();
    }

    public function test(): void
    {
        $dayly = now()->format('Ymd');
        $monthly = now()->format('Ym');
        $yearly = now()->format('Y');
        $total = 'total';

        $this->assertDatabaseMissing('conversion_counts', ['article_id' => $this->article->id, 'type' => '1', 'period' => $dayly]);
        $this->assertDatabaseMissing('conversion_counts', ['article_id' => $this->article->id, 'type' => '2', 'period' => $monthly]);
        $this->assertDatabaseMissing('conversion_counts', ['article_id' => $this->article->id, 'type' => '3', 'period' => $yearly]);
        $this->assertDatabaseMissing('conversion_counts', ['article_id' => $this->article->id, 'type' => '4', 'period' => $total]);

        $url = '/api/conversion/'.$this->article->id;
        $response = $this->post($url);
        $response->assertOk();

        $this->assertDatabaseHas('conversion_counts', ['article_id' => $this->article->id, 'type' => '1', 'period' => $dayly, 'count' => 1]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $this->article->id, 'type' => '2', 'period' => $monthly, 'count' => 1]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $this->article->id, 'type' => '3', 'period' => $yearly, 'count' => 1]);
        $this->assertDatabaseHas('conversion_counts', ['article_id' => $this->article->id, 'type' => '4', 'period' => $total, 'count' => 1]);
    }
}
