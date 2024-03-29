<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\AnalyticsController;

use Closure;
use Tests\Feature\TestCase;

class IndexTest extends TestCase
{
    /**
     * @dataProvider dataValidation
     */
    public function testValidation(Closure $fn, string $error_field): void
    {
        $this->actingAs($this->user);

        $url = Closure::bind($fn, $this)();

        $testResponse = $this->getJson($url);
        $testResponse->assertJsonValidationErrors($error_field);
    }

    public static function dataValidation(): \Generator
    {
        yield 'idsがnull' => [fn (): string => '/api/mypage/analytics?'.http_build_query(['ids' => null]), 'ids'];
        yield 'idsが空' => [fn (): string => '/api/mypage/analytics?'.http_build_query(['ids' => []]), 'ids'];

        yield 'ids.0が存在しないID' => [fn (): string => '/api/mypage/analytics?'.http_build_query(['ids' => [99999]]), 'ids.0'];
        yield 'ids.0が他人の記事' => [fn (): string => '/api/mypage/analytics?'.http_build_query(['ids' => [$this->article2->id]]), 'ids.0'];

        yield 'typeがnull' => [fn (): string => '/api/mypage/analytics?'.http_build_query(['type' => null]), 'type'];
        yield 'typeが不正' => [fn (): string => '/api/mypage/analytics?'.http_build_query(['type' => 'invalid-type']), 'type'];

        yield 'start_dateがnull' => [fn (): string => '/api/mypage/analytics?'.http_build_query(['start_date' => null]), 'start_date'];
        yield 'start_dateが不正' => [fn (): string => '/api/mypage/analytics?'.http_build_query(['start_date' => 'invalid-start_date']), 'start_date'];

        yield 'end_dateがnull' => [fn (): string => '/api/mypage/analytics?'.http_build_query(['end_date' => null]), 'end_date'];
        yield 'end_dateが不正' => [fn (): string => '/api/mypage/analytics?'.http_build_query(['end_date' => 'invalid-start_date']), 'end_date'];
        yield 'end_dateがstart_dateよりも過去' => [fn (): string => '/api/mypage/analytics?'.http_build_query(['start_date' => now(), 'end_date' => now()->modify('-1 day')]), 'end_date'];
    }

    public function testログイン(): void
    {
        $url = '/api/mypage/analytics';
        $res = $this->getJson($url);
        $res->assertUnauthorized();
    }

    public static function dataValues(): \Generator
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
    public function testValues(Closure $fn_pv, Closure $fn_cv, array $param): void
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
        $url = '/api/mypage/analytics?'.http_build_query($params);
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
