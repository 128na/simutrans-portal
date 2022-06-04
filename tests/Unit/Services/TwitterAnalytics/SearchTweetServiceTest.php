<?php

namespace Tests\Unit\Services\TwitterAnalytics;

use App\Services\TwitterAnalytics\SearchTweetService;
use App\Services\TwitterAnalytics\TwitterV2Api;
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

        $data = new stdClass();
        $data->id = '123';
        $data->text = "新規投稿「dummy」\n";
        $data->created_at = '2022-01-01T23:59:59+09:00';
        $data->public_metrics = $publicMetrics;

        $meta = new stdClass();
        if ($paginationToken) {
            $meta->next_token = $paginationToken;
        }
        $response = new stdClass();
        $response->data = [$data];
        $response->meta = $meta;

        return $response;
    }

    public function testSearchTweetsByUsername()
    {
        $this->mock(TwitterV2Api::class, function (MockInterface $m) {
            $m->shouldReceive('setApiVersion')->withArgs(['2']);

            $m->shouldReceive('get')->withArgs([
                'tweets/search/recent', [
                    'query' => 'from:user',
                    'tweet.fields' => 'text,public_metrics,created_at',
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
            $m->shouldReceive('setApiVersion')->withArgs(['2']);

            $m->shouldReceive('get')->withArgs([
                'lists/123/tweets', [
                    'tweet.fields' => 'text,public_metrics,created_at',
                    'max_results' => 100,
                ],
            ])->andReturn($this->createMockData('dummy_token'));

            $m->shouldReceive('get')->withArgs([
                'lists/123/tweets', [
                    'tweet.fields' => 'text,public_metrics,created_at',
                    'max_results' => 100,
                    'pagination_token' => 'dummy_token',
                ],
            ])->andReturn($this->createMockData());
        });

        $service = $this->getSUT();

        $response = $service->searchTweetsByList('123');

        $this->assertCount(2, $response);
    }
}
