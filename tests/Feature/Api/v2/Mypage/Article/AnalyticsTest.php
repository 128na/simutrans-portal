<?php

namespace Tests\Feature\Api\v2\Mypage\Article;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }
    public function testAnalytics()
    {
        $user = factory(User::class)->create();
        $article = factory(Article::class)->create(['user_id' => $user->id]);

        $url = route('api.v2.analytics.index');
        $res = $this->getJson($url);
        $res->assertUnauthorized();

        $this->actingAs($user);

        $url = route('api.v2.analytics.index', ['ids' => null]);
        $res = $this->getJson($url);
        $res->assertJsonValidationErrors(['ids']);
        $url = route('api.v2.analytics.index', ['ids' => []]);
        $res = $this->getJson($url);
        $res->assertJsonValidationErrors(['ids']);

        $url = route('api.v2.analytics.index', ['ids' => [99999]]);
        $res = $this->getJson($url);
        $res->assertJsonValidationErrors(['ids.0']);
        $other_user = factory(User::class)->create();
        $other_article = factory(Article::class)->create(['user_id' => $other_user->id]);
        $url = route('api.v2.analytics.index', ['ids' => [$other_article->id]]);
        $res = $this->getJson($url);
        $res->assertJsonValidationErrors(['ids.0']);

        $url = route('api.v2.analytics.index', ['type' => null]);
        $res = $this->getJson($url);
        $res->assertJsonValidationErrors(['type']);
        $url = route('api.v2.analytics.index', ['type' => 'invalid-type']);
        $res = $this->getJson($url);
        $res->assertJsonValidationErrors(['type']);

        $url = route('api.v2.analytics.index', ['start_date' => null]);
        $res = $this->getJson($url);
        $res->assertJsonValidationErrors(['start_date']);
        $url = route('api.v2.analytics.index', ['start_date' => 'invalid-start_date']);
        $res = $this->getJson($url);
        $res->assertJsonValidationErrors(['start_date']);

        $url = route('api.v2.analytics.index', ['end_date' => null]);
        $res = $this->getJson($url);
        $res->assertJsonValidationErrors(['end_date']);
        $url = route('api.v2.analytics.index', ['end_date' => 'invalid-end_date']);
        $res = $this->getJson($url);
        $res->assertJsonValidationErrors(['end_date']);
        $start_date = now();
        $end_date = now()->modify('-1 day');
        $url = route('api.v2.analytics.index', ['start_date' => $start_date, 'end_date' => $end_date]);
        $res = $this->getJson($url);
        $res->assertJsonValidationErrors(['end_date']);
    }

    public function testValues()
    {
        $user = factory(User::class)->create();
        $article = factory(Article::class)->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $now = now();
        $dailyViewCount = $article->dailyViewCounts()->create(['type' => 1, 'period' => $now->format('Ymd'), 'count' => 72]);
        $monthlyViewCount = $article->monthlyViewCounts()->create(['type' => 2, 'period' => $now->format('Ym'), 'count' => 334]);
        $yearlyViewCount = $article->yearlyViewCounts()->create(['type' => 3, 'period' => $now->format('Y'), 'count' => 114514]);
        $dailyConversionCount = $article->dailyConversionCounts()->create(['type' => 1, 'period' => $now->format('Ymd'), 'count' => 64]);
        $monthlyConversionCount = $article->monthlyConversionCounts()->create(['type' => 2, 'period' => $now->format('Ym'), 'count' => 128]);
        $yearlyConversionCount = $article->yearlyConversionCounts()->create(['type' => 3, 'period' => $now->format('Y'), 'count' => 256]);

        $params = [
            'ids' => [$article->id],
            'type' => 'daily',
            'start_date' => $now->modify('-1 day')->format('Y-m-d'),
            'end_date' => $now->modify('+1 day')->format('Y-m-d'),
        ];
        $url = route('api.v2.analytics.index', $params);
        $res = $this->getJson($url);
        $res->assertExactJson(['data' => [
            [
                $article->id,
                [$dailyConversionCount->period => $dailyConversionCount->count],
                [$dailyViewCount->period => $dailyViewCount->count],
            ],
        ]]);

        $params = [
            'ids' => [$article->id],
            'type' => 'monthly',
            'start_date' => $now->modify('-1 day')->format('Y-m-d'),
            'end_date' => $now->modify('+1 day')->format('Y-m-d'),
        ];
        $url = route('api.v2.analytics.index', $params);
        $res = $this->getJson($url);
        $res->assertExactJson(['data' => [
            [
                $article->id,
                [$monthlyConversionCount->period => $monthlyConversionCount->count],
                [$monthlyViewCount->period => $monthlyViewCount->count],
            ],
        ]]);

        $params = [
            'ids' => [$article->id],
            'type' => 'yearly',
            'start_date' => $now->modify('-1 day')->format('Y-m-d'),
            'end_date' => $now->modify('+1 day')->format('Y-m-d'),
        ];
        $url = route('api.v2.analytics.index', $params);
        $res = $this->getJson($url);
        $res->assertExactJson(['data' => [
            [
                $article->id,
                [$yearlyConversionCount->period => $yearlyConversionCount->count],
                [$yearlyViewCount->period => $yearlyViewCount->count],
            ],
        ]]);
    }
}
