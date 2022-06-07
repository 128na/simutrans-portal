<?php

namespace Tests\Unit\Services\TwitterAnalytics;

use App\Models\OauthToken;
use App\Repositories\OauthTokenRepository;
use App\Services\TwitterAnalytics\Exceptions\InvalidStateException;
use App\Services\TwitterAnalytics\PKCEService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tests\UnitTestCase;

class PKCEServiceTest extends UnitTestCase
{
    private function getSUT(): PKCEService
    {
        return new PKCEService(
            new Carbon('2022-01-01 00:00:00'),
            app(Client::class),
            app(OauthTokenRepository::class),
            'dummyClientId',
            'dummyClientSecret',
            'dummyCallbackUrl',
        );
    }

    public function testGenerateState()
    {
        $actual = $this->getSUT()->generateState(32);
        $this->assertEquals(32, strlen($actual));
    }

    public function testGenerateCodeVerifier()
    {
        $actual = $this->getSUT()->generateCodeVerifier(32);
        $this->assertTrue(strlen($actual) >= 43);
        $this->assertTrue(strlen($actual) <= 128);
    }

    public function testGenerateCodeChallenge()
    {
        $actual = $this->getSUT()->generateCodeChallenge('dummy');
        $this->assertTrue(strlen($actual) >= 43);
        $this->assertTrue(strlen($actual) <= 128);
    }

    public function testGenerateAuthorizeUrl()
    {
        $actual = $this->getSUT()->generateAuthorizeUrl('dummyState', 'dummyCodeChallange');
        $expected = 'https://twitter.com/i/oauth2/authorize?response_type=code&client_id=dummyClientId&redirect_uri=dummyCallbackUrl&scope=users.read%20tweet.read%20list.read%20offline.access&state=dummyState&code_challenge=dummyCodeChallange&code_challenge_method=S256';
        $this->assertEquals($expected, $actual);
    }

    public function testVerifyState()
    {
        $this->getSUT()->verifyState('dummyState', 'dummyState');
        $this->assertTrue(true);
    }

    public function testVerifyState不一致()
    {
        $this->expectException(InvalidStateException::class);
        $this->getSUT()->verifyState('dummyState', 'dummyState2');
    }

    public function testGenerateToken()
    {
        $this->mock(Client::class, function (MockInterface $m) {
            $m->shouldReceive('request')->withAnyArgs([
                'POST',
                'https://api.twitter.com/2/oauth2/token',
                [
                    'auth' => ['dummyClientId', 'dummyClientSecret'],
                    'form_params' => [
                        'code' => 'dummyCode',
                        'grant_type' => 'authorization_code',
                        'redirect_uri' => 'dummyCallbackUrl',
                        'code_verifier' => 'dummyCodeVerifier',
                    ],
                ],
            ])->andReturn($this->mock(ResponseInterface::class, function (MockInterface $m) {
                $m->shouldReceive('getBody')->andReturn($this->mock(StreamInterface::class, function (MockInterface $m) {
                    $m->shouldReceive('getContents')
                        ->andReturn('{"token_type":"dummyType","scope":"dummyScope","access_token":"dummyAccessToken","refresh_token":"dummyRefreshToken","expires_in":"7200"}');
                }));
            }));
        });

        $this->mock(OauthTokenRepository::class, function (MockInterface $m) {
            $m->shouldReceive('updateOrCreate')
                ->withArgs([
                    ['application' => 'twitter'],
                    [
                        'token_type' => 'dummyType',
                        'scope' => 'dummyScope',
                        'access_token' => 'dummyAccessToken',
                        'refresh_token' => 'dummyRefreshToken',
                        'expired_at' => new Carbon('2022-01-01 02:00:00'),
                    ],
                ])
                ->andReturn(new OauthToken());
        });

        $this->getSUT()->generateToken('dummyCode', 'dummyCodeVerifier');
    }

    public function testRefreshToken()
    {
        $this->mock(Client::class, function (MockInterface $m) {
            $m->shouldReceive('request')->withAnyArgs([
                'POST',
                'https://api.twitter.com/2/oauth2/token',
                [
                    'auth' => ['dummyClientId', 'dummyClientSecret'],
                    'form_params' => [
                        'refresh_token' => 'dummyRefreshToken',
                        'grant_type' => 'refresh_token',
                    ],
                ],
            ])->andReturn($this->mock(ResponseInterface::class, function (MockInterface $m) {
                $m->shouldReceive('getBody')->andReturn($this->mock(StreamInterface::class, function (MockInterface $m) {
                    $m->shouldReceive('getContents')
                        ->andReturn('{"token_type":"dummyType","scope":"dummyScope","access_token":"dummyAccessToken","refresh_token":"dummyRefreshToken2","expires_in":"7200"}');
                }));
            }));
        });

        $this->mock(OauthTokenRepository::class, function (MockInterface $m) {
            $m->shouldReceive('updateOrCreate')
                ->withArgs([
                    ['application' => 'twitter'],
                    [
                        'token_type' => 'dummyType',
                        'scope' => 'dummyScope',
                        'access_token' => 'dummyAccessToken',
                        'refresh_token' => 'dummyRefreshToken2',
                        'expired_at' => new Carbon('2022-01-01 02:00:00'),
                    ],
                ])
                ->andReturn(new OauthToken());
        });

        $this->getSUT()->refreshToken(new OauthToken(['refresh_token' => 'dummyRefreshToken']));
    }

    public function testRevokeToken()
    {
        $this->mock(Client::class, function (MockInterface $m) {
            $m->shouldReceive('request')->withAnyArgs([
                'POST',
                'https://api.twitter.com/2/oauth2/revoke',
                [
                    'auth' => ['dummyClientId', 'dummyClientSecret'],
                    'form_params' => [
                        'token' => 'dummyAccessToken',
                        'token_type_hint' => 'access_token',
                    ],
                ],
            ]);
        });

        $this->mock(OauthTokenRepository::class, function (MockInterface $m) {
            $m->shouldReceive('delete');
        });

        $this->getSUT()->revokeToken(new OauthToken(['access_token' => 'dummyAccessToken']));
    }

    public function testRevokeTokenAPIエラーでもトークン削除される()
    {
        $this->mock(Client::class, function (MockInterface $m) {
            $m->shouldReceive('request')
                ->andThrow(new ClientException('dummy', new Request('post', ''), new Response()));
        });

        $this->mock(OauthTokenRepository::class, function (MockInterface $m) {
            $m->shouldReceive('delete');
        });

        $this->getSUT()->revokeToken(new OauthToken(['access_token' => 'dummyAccessToken']));
    }
}
