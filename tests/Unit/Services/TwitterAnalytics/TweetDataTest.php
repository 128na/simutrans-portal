<?php

namespace Tests\Unit\Services\TwitterAnalytics;

use App\Services\TwitterAnalytics\InvalidTweetDataException;
use App\Services\TwitterAnalytics\TweetData;
use stdClass;
use Tests\UnitTestCase;

class TweetDataTest extends UnitTestCase
{
    public function test新規投稿()
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

        $tweetData = new TweetData($data);

        $this->assertEquals('123', $tweetData->id);
        $this->assertEquals("新規投稿「dummy」\n", $tweetData->text);
        $this->assertEquals(1, $tweetData->retweetCount);
        $this->assertEquals(2, $tweetData->replyCount);
        $this->assertEquals(3, $tweetData->likeCount);
        $this->assertEquals(4, $tweetData->quoteCount);
        $this->assertEquals(5, $tweetData->impressionCount);
        $this->assertEquals(6, $tweetData->urlLinkClicks);
        $this->assertEquals(7, $tweetData->userProfileClicks);
        $this->assertEquals('dummy', $tweetData->title);
        $this->assertEquals(true, $tweetData->isNew);
        $this->assertTrue($tweetData->createdAt->eq('2022-01-01T23:59:59+09:00'));
    }

    public function test更新()
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
        $data->text = "「dummy」更新\n";
        $data->created_at = '2022-01-01T23:59:59+09:00';
        $data->public_metrics = $publicMetrics;
        $data->non_public_metrics = $nonPublicMetrics;

        $tweetData = new TweetData($data);

        $this->assertEquals('123', $tweetData->id);
        $this->assertEquals("「dummy」更新\n", $tweetData->text);
        $this->assertEquals(1, $tweetData->retweetCount);
        $this->assertEquals(2, $tweetData->replyCount);
        $this->assertEquals(3, $tweetData->likeCount);
        $this->assertEquals(4, $tweetData->quoteCount);
        $this->assertEquals(5, $tweetData->impressionCount);
        $this->assertEquals(6, $tweetData->urlLinkClicks);
        $this->assertEquals(7, $tweetData->userProfileClicks);
        $this->assertEquals('dummy', $tweetData->title);
        $this->assertEquals(false, $tweetData->isNew);
        $this->assertTrue($tweetData->createdAt->eq('2022-01-01T23:59:59+09:00'));
    }

    public function test不正フォーマット()
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
        $data->text = "dummy\n";
        $data->created_at = '2022-01-01T23:59:59+09:00';
        $data->public_metrics = $publicMetrics;
        $data->non_public_metrics = $nonPublicMetrics;

        $this->expectException(InvalidTweetDataException::class);

        new TweetData($data);
    }

    public function testNonPublicMetrics無し()
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

        $tweetData = new TweetData($data);

        $this->assertEquals('123', $tweetData->id);
        $this->assertEquals("新規投稿「dummy」\n", $tweetData->text);
        $this->assertEquals(1, $tweetData->retweetCount);
        $this->assertEquals(2, $tweetData->replyCount);
        $this->assertEquals(3, $tweetData->likeCount);
        $this->assertEquals(4, $tweetData->quoteCount);
        $this->assertEquals(0, $tweetData->impressionCount);
        $this->assertEquals(0, $tweetData->urlLinkClicks);
        $this->assertEquals(0, $tweetData->userProfileClicks);
        $this->assertEquals('dummy', $tweetData->title);
        $this->assertEquals(true, $tweetData->isNew);
        $this->assertTrue($tweetData->createdAt->eq('2022-01-01T23:59:59+09:00'));
    }
}
