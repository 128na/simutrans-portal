<?php

namespace Tests\Unit\Services\TwitterAnalytics;

use App\Models\OauthToken;
use App\Repositories\OauthTokenRepository;
use App\Services\TwitterAnalytics\Exceptions\PKCETokenNotFoundException;
use App\Services\TwitterAnalytics\Exceptions\PKCETokenRefreshFailedException;
use App\Services\TwitterAnalytics\PKCEService;
use App\Services\TwitterAnalytics\TwitterV2Api;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery\MockInterface;
use Tests\UnitTestCase;

class TwitterV2ApiTest extends UnitTestCase
{
    private function getSUT(): TwitterV2Api
    {
        return new TwitterV2Api(
            'dummyConsumerKey',
            'dummyConsumerSecret',
            'appOnlyBearerToken',
            app(OauthTokenRepository::class),
            app(PKCEService::class),
        );
    }

    public function testIsPkceToken()
    {
        $client = $this->getSUT();
        $this->assertFalse($client->isPkceToken());
    }

    public function testApplyToken()
    {
        $this->mock(OauthTokenRepository::class, function (MockInterface $m) {
            $m->shouldReceive('getToken')->withArgs(['twitter'])->andReturn(new OauthToken(['access_token' => 'dummy']));
        });

        $client = $this->getSUT();
        $client->applyPKCEToken();
        $this->assertTrue($client->isPkceToken());
    }

    public function testApplyTokenトークン更新()
    {
        $this->mock(OauthTokenRepository::class, function (MockInterface $m) {
            $m->shouldReceive('getToken')->withArgs(['twitter'])->andReturn(new OauthToken(['expired_at' => now()->yesterday()]));
        });
        $this->mock(PKCEService::class, function (MockInterface $m) {
            $m->shouldReceive('refreshToken')->andThrow(new OauthToken(['access_token' => 'dummy']));
        });

        $client = $this->getSUT();
        $client->applyPKCEToken();
        $this->assertTrue($client->isPkceToken());
    }

    public function testApplyTokenトークン無し()
    {
        $this->mock(OauthTokenRepository::class, function (MockInterface $m) {
            $m->shouldReceive('getToken')->withArgs(['twitter'])->andThrow(new ModelNotFoundException());
        });

        $this->expectException(PKCETokenNotFoundException::class);

        $client = $this->getSUT();
        $client->applyPKCEToken();
    }

    public function testApplyTokenトークン更新失敗()
    {
        $this->mock(OauthTokenRepository::class, function (MockInterface $m) {
            $m->shouldReceive('getToken')->withArgs(['twitter'])->andReturn(new OauthToken(['expired_at' => now()->yesterday()]));
        });
        $this->mock(PKCEService::class, function (MockInterface $m) {
            $m->shouldReceive('refreshToken')->andThrow(new ClientException('dummy', new Request('post', ''), new Response()));
            $m->shouldReceive('revokeToken')->andReturn(new OauthToken());
        });

        $this->expectException(PKCETokenRefreshFailedException::class);

        $client = $this->getSUT();
        $client->applyPKCEToken();
    }
}
