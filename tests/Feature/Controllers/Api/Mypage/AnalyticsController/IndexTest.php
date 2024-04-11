<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\AnalyticsController;

use App\Models\Article;
use Tests\Feature\TestCase;

final class IndexTest extends TestCase
{
    private Article $article;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->article = Article::factory()->create(['published_at' => now()]);
    }

    public function test(): void
    {
        $this->actingAs($this->article->user);

        $url = '/api/mypage/analytics?'.http_build_query([
            'ids' => [$this->article->id],
            'type' => 'daily',
            'start_date' => now()->yesterday()->toDateTimeString(),
            'end_date' => now()->toDateTimeString(),
        ]);

        $testResponse = $this->getJson($url);
        $testResponse->assertOk();
        $testResponse->assertSee($this->article->id);
    }

    public function test未ログイン(): void
    {
        $url = '/api/mypage/analytics';
        $res = $this->getJson($url);
        $res->assertUnauthorized();
    }
}
