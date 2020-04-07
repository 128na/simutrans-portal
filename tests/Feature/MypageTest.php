<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MypageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    /**
     * アナリティクス画面が正常に表示されること
     * 記事が0件
     * 記事が1件
     *      PV: 0,
     *      PV: 1,
     *      PV: 2,
     *      CV: 0,
     *      CV: 1,
     *      CV: 2,
     * 記事が2件以上
     */
    // public function testAnalytics()
    // {
    //     $user = factory(User::class)->create();
    //     $this->actingAs($user);

    //     $path = 'mypage/articles/analytics';

    //     $response = $this->get($path);
    //     $response->assertOk();

    //     $first_article = factory(Article::class)->create(['user_id' => $user->id]);
    //     $response = $this->get($path);
    //     $response->assertOk();

    //     ViewCount::countUp($first_article);
    //     ConversionCount::countUp($first_article);
    //     $response = $this->get($path);
    //     $response->assertOk();

    //     ViewCount::countUp($first_article);
    //     ConversionCount::countUp($first_article);
    //     $response = $this->get($path);
    //     $response->assertOk();

    //     $second_article = factory(Article::class)->create(['user_id' => $user->id]);
    //     $response = $this->get($path);
    //     $response->assertOk();
    // }
}
