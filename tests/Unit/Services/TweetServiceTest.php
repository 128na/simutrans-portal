<?php

namespace Tests\Unit\Services;

use App\Services\TweetFailedException;
use App\Services\TweetService;
use App\Services\TwitterAnalytics\TwitterV1Api;
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

    public function testPost()
    {
        $this->mock(TwitterV1Api::class, function (MockInterface $m) {
            $m->shouldReceive('post')->withArgs([
                'statuses/update',
                ['status' => 'dummy'],
            ]);
        });
        $this->getSUT()->post('dummy');
    }

    public function testPost投稿失敗()
    {
        $this->mock(TwitterV1Api::class, function (MockInterface $m) {
            $response = new stdClass();
            $response->errors = ['dummy'];
            $m->shouldReceive('post')->withArgs([
                'statuses/update',
                ['status' => 'dummy'],
            ])->andReturn($response);
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
            ])->andReturn($response);

            $m->shouldReceive('post')->withArgs([
                'statuses/update',
                ['status' => 'dummy', 'media_ids' => 'http://example.com/dummy.png'],
            ]);
        });
        $this->getSUT()->postMedia(['dummy.png'], 'dummy');
    }

    public function testPostMedia画像アップロード失敗()
    {
        $this->mock(TwitterV1Api::class, function (MockInterface $m) {
            $response = new stdClass();
            $response->errors = ['dummy'];
            $m->shouldReceive('upload')->withArgs([
                'media/upload',
                ['media' => 'dummy.png'],
            ])->andReturn($response);
        });

        $this->expectException(TweetFailedException::class);
        $this->getSUT()->postMedia(['dummy.png'], 'dummy');
    }
}
