<?php

namespace Tests\Feature\Api\v2\Mypage\Article;

use App\Models\Article;
use App\Models\User;
use Closure;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    private User $user2;
    private Article $article2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user2 = User::factory()->create();
        $this->article2 = Article::factory()->create(['user_id' => $this->user2->id]);
    }

    /**
     * @dataProvider dataValidation
     */
    public function testValidation(Closure $fn, string $error_field)
    {
        $this->actingAs($this->user);

        $url = Closure::bind($fn, $this)();

        $res = $this->getJson($url);
        $res->assertJsonValidationErrors($error_field);
    }

    public function dataValidation()
    {
        yield 'idsがnull' => [fn () => route('api.v2.analytics.index', ['ids' => null]), 'ids'];
        yield 'idsが空' => [fn () => route('api.v2.analytics.index', ['ids' => []]), 'ids'];

        yield 'ids.0が存在しないID' => [fn () => route('api.v2.analytics.index', ['ids' => [99999]]), 'ids.0'];
        yield 'ids.0が他人の記事' => [fn () => route('api.v2.analytics.index', ['ids' => [$this->article2->id]]), 'ids.0'];

        yield 'typeがnull' => [fn () => route('api.v2.analytics.index', ['type' => null]), 'type'];
        yield 'typeが不正' => [fn () => route('api.v2.analytics.index', ['type' => 'invalid-type']), 'type'];

        yield 'start_dateがnull' => [fn () => route('api.v2.analytics.index', ['start_date' => null]), 'start_date'];
        yield 'start_dateが不正' => [fn () => route('api.v2.analytics.index', ['start_date' => 'invalid-start_date']), 'start_date'];

        yield 'end_dateがnull' => [fn () => route('api.v2.analytics.index', ['end_date' => null]), 'end_date'];
        yield 'end_dateが不正' => [fn () => route('api.v2.analytics.index', ['end_date' => 'invalid-end_date']), 'end_date'];
        yield 'end_dateがstart_dateよりも過去' => [fn () => route('api.v2.analytics.index', ['start_date' => now(), 'end_date' => now()->modify('-1 day')]), 'end_date'];
    }

    public function testログイン()
    {
        $url = route('api.v2.analytics.index');
        $res = $this->getJson($url);
        $res->assertUnauthorized();
    }

    public function dataValues()
    {
        $now = now();
        yield 'daily' => [
            fn () => $this->article->dailyViewCounts()->create(['type' => 1, 'period' => $now->format('Ymd'), 'count' => 72]),
            fn () => $this->article->dailyConversionCounts()->create(['type' => 1, 'period' => $now->format('Ymd'), 'count' => 64]),
            ['type' => 'daily'],
        ];
        yield 'monthly' => [
            fn () => $this->article->monthlyViewCounts()->create(['type' => 2, 'period' => $now->format('Ym'), 'count' => 334]),
            fn () => $this->article->monthlyConversionCounts()->create(['type' => 2, 'period' => $now->format('Ym'), 'count' => 128]),
            ['type' => 'monthly'],
        ];
        yield 'yearly' => [
            fn () => $this->article->yearlyViewCounts()->create(['type' => 3, 'period' => $now->format('Y'), 'count' => 114514]),
            fn () => $this->article->yearlyConversionCounts()->create(['type' => 3, 'period' => $now->format('Y'), 'count' => 256]),
            ['type' => 'yearly'],
        ];
    }

    /**
     * @dataProvider dataValues
     */
    public function testValues(Closure $fn_pv, Closure $fn_cv, array $param)
    {
        $now = now();
        $this->actingAs($this->user);

        $pv = Closure::bind($fn_pv, $this)();
        $cv = Closure::bind($fn_cv, $this)();

        $params = array_merge([
            'ids' => [$this->article->id],
            'start_date' => $now->modify('-1 day')->format('Y-m-d'),
            'end_date' => $now->modify('+1 day')->format('Y-m-d'),
        ], $param);
        $url = route('api.v2.analytics.index', $params);
        $res = $this->getJson($url);
        $res->assertExactJson(['data' => [
            [
                $this->article->id,
                [$pv->period => $pv->count],
                [$cv->period => $cv->count],
            ],
        ]]);
    }
}
