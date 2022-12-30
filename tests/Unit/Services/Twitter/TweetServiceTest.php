<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Twitter;

use App\Services\Twitter\Exceptions\TweetFailedException;
use App\Services\Twitter\TweetService;
use App\Services\Twitter\TwitterV1Api;
use Mockery\MockInterface;
use stdClass;
use Tests\UnitTestCase;

class TweetServiceTest extends UnitTestCase
{
    private function getSUT(): TweetService
    {
        return new TweetService(
            app(TwitterV1Api::class),
            true
        );
    }

    private function createSuccessResponse(): stdClass
    {
        $res = new stdClass();
        $res->id_str = 'dummyId';
        $res->text = "新規投稿「dummy」\n";
        $res->retweet_count = 1;
        $res->reply_count = 2;
        $res->favorite_count = 3;
        $res->quote_count = 4;
        $res->created_at = '2022-01-01T23:59:59+09:00';

        return $res;
    }

    private function createFailedResponse(): stdClass
    {
        $res = new stdClass();
        $res->errors = ['dummy'];

        return $res;
    }

    public function testPost()
    {
        $this->mock(TwitterV1Api::class, function (MockInterface $m) {
            $m->shouldReceive('post')->withArgs([
                'statuses/update',
                ['status' => 'dummy'],
            ])->once()->andReturn($this->createSuccessResponse());
        });
        $this->getSUT()->post('dummy');
    }

    public function testPost投稿失敗()
    {
        $this->mock(TwitterV1Api::class, function (MockInterface $m) {
            $m->shouldReceive('post')->withArgs([
                'statuses/update',
                ['status' => 'dummy'],
            ])->once()->andReturn($this->createFailedResponse());
        });

        $this->expectException(TweetFailedException::class);
        $this->getSUT()->post('dummy');
    }

    public function testPostMedia()
    {
        $this->mock(TwitterV1Api::class, function (MockInterface $m) {
            $response = new stdClass();
            $response->media_id_string = 'http://example.com/dummy.png';
            $m->shouldReceive('upload')->withArgs([
                'media/upload',
                ['media' => 'dummy.png'],
            ])->once()->andReturn($response);

            $m->shouldReceive('post')->withArgs([
                'statuses/update',
                ['status' => 'dummy', 'media_ids' => 'http://example.com/dummy.png'],
            ])->once()->andReturn($this->createSuccessResponse());
        });
        $this->getSUT()->postMedia(['dummy.png'], 'dummy');
    }

    public function testPostMedia画像アップロード失敗()
    {
        $this->mock(TwitterV1Api::class, function (MockInterface $m) {
            $m->shouldReceive('upload')->withArgs([
                'media/upload',
                ['media' => 'dummy.png'],
            ])->once()->andReturn($this->createFailedResponse());
        });

        $this->expectException(TweetFailedException::class);
        $this->getSUT()->postMedia(['dummy.png'], 'dummy');
    }
}
