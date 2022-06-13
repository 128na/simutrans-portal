<?php

namespace Tests\Unit\Services\Twitter;

use App\Services\Twitter\TweetDataOldFormatSupport;
use stdClass;
use Tests\UnitTestCase;

class TweetDataOldFormatSupportTest extends UnitTestCase
{
    public function test旧フォーマット1()
    {
        $publicMetrics = new stdClass();
        $publicMetrics->retweet_count = 1;
        $publicMetrics->reply_count = 2;
        $publicMetrics->like_count = 3;
        $publicMetrics->quote_count = 4;

        $data = new stdClass();
        $data->id = '123';
        $data->text = "新規投稿アドオン「dummy」 by user\n";
        $data->created_at = '2022-01-01T23:59:59+09:00';
        $data->public_metrics = $publicMetrics;

        $tweetData = new TweetDataOldFormatSupport($data);

        $this->assertEquals('123', $tweetData->id);
        $this->assertEquals("新規投稿アドオン「dummy」 by user\n", $tweetData->text);
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

    public function test旧フォーマット2()
    {
        $publicMetrics = new stdClass();
        $publicMetrics->retweet_count = 1;
        $publicMetrics->reply_count = 2;
        $publicMetrics->like_count = 3;
        $publicMetrics->quote_count = 4;

        $data = new stdClass();
        $data->id = '123';
        $data->text = "新規投稿アドオン「dummy」\n";
        $data->created_at = '2022-01-01T23:59:59+09:00';
        $data->public_metrics = $publicMetrics;

        $tweetData = new TweetDataOldFormatSupport($data);

        $this->assertEquals('123', $tweetData->id);
        $this->assertEquals("新規投稿アドオン「dummy」\n", $tweetData->text);
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
