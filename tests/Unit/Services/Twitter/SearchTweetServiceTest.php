<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Twitter;

use App\Services\Twitter\Exceptions\PKCETokenNotFoundException;
use App\Services\Twitter\Exceptions\PKCETokenRefreshFailedException;
use App\Services\Twitter\SearchTweetService;
use App\Services\Twitter\TwitterV2Api;
use Mockery\MockInterface;
use stdClass;
use Tests\UnitTestCase;

class SearchTweetServiceTest extends UnitTestCase
{
    private function getSUT(): SearchTweetService
    {
        return app(SearchTweetService::class);
    }

    private function createMockData(?string $paginationToken = null): stdClass
    {
        $publicMetrics = new stdClass();
        $publicMetrics->retweet_count = 1;
        $publicMetrics->reply_count = 2;
        $publicMetrics->like_count = 3;
        $publicMetrics->quote_count = 4;

        $nonPublicMetrics = new stdClass();
        $nonPublicMetrics->impression_count = 5;
        $nonPublicMetrics->url_link_clicks = 6;
        $nonPublicMetrics->user_profile_clicks = 7;

        $data = new stdClass();
        $data->id = '123';
        $data->text = "新規投稿「dummy」\n";
        $data->created_at = '2022-01-01T23:59:59+09:00';
        $data->public_metrics = $publicMetrics;
        $data->non_public_metrics = $nonPublicMetrics;

        $meta = new stdClass();
        if ($paginationToken) {
            $meta->next_token = $paginationToken;
        }
        $response = new stdClass();
        $response->data = [$data];
        $response->meta = $meta;

        return $response;
    }

    public function testsearchTweetsByTimelinePkce(): void
    {
        $this->mock(TwitterV2Api::class, function (MockInterface $m) {
            $m->shouldReceive('applyPKCEToken')->once();

            $m->shouldReceive('get')->withArgs([
                'users/dummyId/tweets', [
                    'tweet.fields' => 'text,public_metrics,created_at,non_public_metrics',
                    'max_results' => 100,
                ],
            ])->once()->andReturn($this->createMockData('dummyToken'));

            $m->shouldReceive('get')->withArgs([
                'users/dummyId/tweets', [
                    'tweet.fields' => 'text,public_metrics,created_at,non_public_metrics',
                    'max_results' => 100,
                    'pagination_token' => 'dummyToken',
                ],
            ])->once()->andReturn($this->createMockData(), SearchTweetService::USE_PKCE_TOKEN);
        });

        $service = $this->getSUT();

        $response = $service->searchTweetsByTimeline('dummyId', SearchTweetService::USE_PKCE_TOKEN);

        $this->assertCount(2, $response);
    }

    public function testsearchTweetsByTimelineAppOnly(): void
    {
        $this->mock(TwitterV2Api::class, function (MockInterface $m) {
            $m->shouldReceive('applyPKCEToken')->never();

            $m->shouldReceive('get')->withArgs([
                'users/dummyId/tweets', [
                    'tweet.fields' => 'text,public_metrics,created_at',
                    'max_results' => 100,
                ],
            ])->once()->andReturn($this->createMockData());
        });

        $service = $this->getSUT();

        $response = $service->searchTweetsByTimeline('dummyId', SearchTweetService::USE_APP_ONLY_TOKEN);

        $this->assertCount(1, $response);
    }

    public function testPKCEToken無し(): void
    {
        $this->mock(TwitterV2Api::class, function (MockInterface $m) {
            $m->shouldReceive('applyPKCEToken')->once()->andThrow(new PKCETokenNotFoundException());
        });

        $this->expectException(PKCETokenNotFoundException::class);
        $service = $this->getSUT();

        $service->searchTweetsByTimeline('dummyId', SearchTweetService::USE_PKCE_TOKEN);
    }

    public function testPKCEToken更新失敗(): void
    {
        $this->mock(TwitterV2Api::class, function (MockInterface $m) {
            $m->shouldReceive('applyPKCEToken')->once()->andThrow(new PKCETokenRefreshFailedException());
        });

        $this->expectException(PKCETokenRefreshFailedException::class);
        $service = $this->getSUT();

        $service->searchTweetsByTimeline('dummyId', SearchTweetService::USE_PKCE_TOKEN);
    }
}
