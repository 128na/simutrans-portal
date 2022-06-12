<?php

namespace Tests\Unit\Services\Twitter;

use App\Repositories\Article\TweetLogRepository;
use App\Repositories\Article\TweetLogSummaryRepository;
use App\Services\Twitter\AggregateTweetLogService;
use App\Services\Twitter\TweetData;
use Carbon\Carbon;
use Illuminate\Support\LazyCollection;
use Mockery\MockInterface;
use stdClass;
use Tests\UnitTestCase;

class AggregateTweetLogServiceTest extends UnitTestCase
{
    private function getSUT(): AggregateTweetLogService
    {
        return app(AggregateTweetLogService::class);
    }

    /**
     * @return TweetData[]
     */
    private function createMockData(): array
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

        $tweetData = new TweetData($data);
        $tweetData->articleId = 1;

        return [$tweetData];
    }

    public function testUpdateOrCreateTweetLogs()
    {
        $this->mock(TweetLogRepository::class, function (MockInterface $m) {
            $m->shouldReceive('updateOrCreate')->withArgs([
                ['id' => '123'],
                [
                    'article_id' => 1,
                    'text' => "新規投稿「dummy」\n",
                    'retweet_count' => 1,
                    'reply_count' => 2,
                    'like_count' => 3,
                    'quote_count' => 4,
                    'impression_count' => 5,
                    'url_link_clicks' => 6,
                    'user_profile_clicks' => 7,
                    'tweet_created_at' => Carbon::parse('2022-01-01T23:59:59+09:00'),
                ],
            ]);
        });

        $service = $this->getSUT();

        $response = $service->updateOrCreateTweetLogs($this->createMockData());

        $this->assertCount(1, $response);
        $this->assertEquals(1, $response[0]);
    }

    public function testFirstOrCreateTweetLogs()
    {
        $this->mock(TweetLogRepository::class, function (MockInterface $m) {
            $m->shouldReceive('firstOrCreate')->withArgs([
                ['id' => '123'],
                [
                    'article_id' => 1,
                    'text' => "新規投稿「dummy」\n",
                    'retweet_count' => 1,
                    'reply_count' => 2,
                    'like_count' => 3,
                    'quote_count' => 4,
                    'impression_count' => 5,
                    'url_link_clicks' => 6,
                    'user_profile_clicks' => 7,
                    'tweet_created_at' => Carbon::parse('2022-01-01T23:59:59+09:00'),
                ],
            ]);
        });

        $service = $this->getSUT();

        $response = $service->firstOrCreateTweetLogs($this->createMockData());

        $this->assertCount(1, $response);
        $this->assertEquals(1, $response[0]);
    }

    public function testUpdateOrCreateTweetLogSummary()
    {
        $this->mock(TweetLogRepository::class, function (MockInterface $m) {
            $m->shouldReceive('cursorTweetLogSummary')->withArgs([[1]])
                ->andReturn(LazyCollection::make(function () {
                    $data = new stdClass();
                    $data->article_id = 1;
                    $data->total_retweet_count = 1;
                    $data->total_reply_count = 2;
                    $data->total_like_count = 3;
                    $data->total_quote_count = 4;
                    $data->total_impression_count = 5;
                    $data->total_url_link_clicks = 6;
                    $data->total_user_profile_clicks = 7;
                    yield $data;
                }));
        });

        $this->mock(TweetLogSummaryRepository::class, function (MockInterface $m) {
            $m->shouldReceive('updateOrCreate')->withArgs([
                ['article_id' => 1],
                [
                    'total_retweet_count' => 1,
                    'total_reply_count' => 2,
                    'total_like_count' => 3,
                    'total_quote_count' => 4,
                    'total_impression_count' => 5,
                    'total_url_link_clicks' => 6,
                    'total_user_profile_clicks' => 7,
                ],
            ]);
        });

        $service = $this->getSUT();
        $service->updateOrCreateTweetLogSummary([1]);
    }
}
