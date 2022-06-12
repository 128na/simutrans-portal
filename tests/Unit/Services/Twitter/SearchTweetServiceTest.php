<?php

namespace Tests\Unit\Services\Twitter;

use App\Services\Twitter\Exceptions\PKCETokenNotFoundException;
use App\Services\Twitter\Exceptions\PKCETokenRefreshFailedException;
use App\Services\Twitter\Exceptions\TooManyIdsException;
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
        $data->id_str = '123';
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

    public function testPKCEToken無し()
    {
        $this->mock(TwitterV2Api::class, function (MockInterface $m) {
            $m->shouldReceive('applyPKCEToken')->andThrow(new PKCETokenNotFoundException());
        });

        $this->getSUT();
        $this->assertTrue(true);
    }

    public function testPKCEToken更新失敗()
    {
        $this->mock(TwitterV2Api::class, function (MockInterface $m) {
            $m->shouldReceive('applyPKCEToken')->andThrow(new PKCETokenRefreshFailedException());
        });

        $this->getSUT();
        $this->assertTrue(true);
    }

    public function testSearchTweetsByUsername()
    {
        $this->mock(TwitterV2Api::class, function (MockInterface $m) {
            $m->shouldReceive('applyPKCEToken');
            $m->shouldReceive('setApiVersion')->withArgs(['2']);

            $m->shouldReceive('get')->withArgs([
                'tweets/search/recent', [
                    'query' => 'from:user',
                    'tweet.fields' => 'text,public_metrics,created_at,non_public_metrics',
                    'max_results' => 100,
                ],
            ])->andReturn($this->createMockData());
        });

        $service = $this->getSUT();

        $response = $service->searchTweetsByUsername('user');

        $this->assertCount(1, $response);
    }

    public function testSearchTweetsByList()
    {
        $this->mock(TwitterV2Api::class, function (MockInterface $m) {
            $m->shouldReceive('applyPKCEToken');
            $m->shouldReceive('setApiVersion')->withArgs(['2']);

            $m->shouldReceive('get')->withArgs([
                'lists/123/tweets', [
                    'tweet.fields' => 'text,public_metrics,created_at,non_public_metrics',
                    'max_results' => 100,
                ],
            ])->andReturn($this->createMockData('dummy_token'));

            $m->shouldReceive('get')->withArgs([
                'lists/123/tweets', [
                    'tweet.fields' => 'text,public_metrics,created_at,non_public_metrics',
                    'max_results' => 100,
                    'pagination_token' => 'dummy_token',
                ],
            ])->andReturn($this->createMockData());
        });

        $service = $this->getSUT();

        $response = $service->searchTweetsByList('123');

        $this->assertCount(2, $response);
    }

    public function testSearchTweetsByIds()
    {
        $this->mock(TwitterV2Api::class, function (MockInterface $m) {
            $m->shouldReceive('applyPKCEToken');
            $m->shouldReceive('setApiVersion')->withArgs(['2']);
            $m->shouldReceive('get')->withArgs([
                'tweets', [
                    'ids' => '123,456',
                    'tweet.fields' => 'text,public_metrics,created_at',
                ],
            ])->andReturn($this->createMockData());
        });

        $service = $this->getSUT();

        $response = $service->searchTweetsByIds(['123', '456']);

        $this->assertCount(1, $response);
    }

    public function testSearchTweetsByIdsID101個以上()
    {
        $this->mock(TwitterV2Api::class, function (MockInterface $m) {
            $m->shouldReceive('applyPKCEToken');
            $m->shouldReceive('setApiVersion')->withArgs(['2']);
        });

        $this->expectException(TooManyIdsException::class);

        $service = $this->getSUT();

        $ids = array_map(fn ($n) => (string) $n, range(1, 101));
        $this->assertCount(101, $ids);

        $service->searchTweetsByIds($ids);
    }
}
