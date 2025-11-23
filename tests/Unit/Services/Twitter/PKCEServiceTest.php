<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Twitter;

use App\Models\OauthToken;
use App\Repositories\OauthTokenRepository;
use App\Services\Twitter\Exceptions\InvalidStateException;
use App\Services\Twitter\PKCEService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

final class PKCEServiceTest extends TestCase
{
    private function getSUT(
        ?Carbon $now = null,
        ?Client $client = null,
        ?OauthTokenRepository $repository = null,
    ): PKCEService {
        return new PKCEService(
            now: $now ?? Carbon::parse('2024-01-01 00:00:00'),
            client: $client ?? $this->mock(Client::class),
            oauthTokenRepository: $repository ?? $this->mock(OauthTokenRepository::class),
            clientId: 'test_client_id',
            clientSecret: 'test_client_secret',
            callbackUrl: 'https://example.com/callback',
        );
    }

    public function test_generate_state_returns_random_string(): void
    {
        $sut = $this->getSUT();
        $state = $sut->generateState();

        $this->assertIsString($state);
        $this->assertEquals(32, strlen($state));
    }

    public function test_generate_state_with_custom_length(): void
    {
        $sut = $this->getSUT();
        $state = $sut->generateState(64);

        $this->assertIsString($state);
        $this->assertEquals(64, strlen($state));
    }

    public function test_generate_code_verifier_returns_valid_format(): void
    {
        $sut = $this->getSUT();
        $verifier = $sut->generateCodeVerifier();

        $this->assertIsString($verifier);
        // Base64 URL-safe encoding without padding
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]+$/', $verifier);
    }

    public function test_generate_code_challenge_from_verifier(): void
    {
        $sut = $this->getSUT();
        $verifier = 'test_verifier_12345';
        $challenge = $sut->generateCodeChallenge($verifier);

        $this->assertIsString($challenge);
        // Base64 URL-safe encoding without padding
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9_-]+$/', $challenge);
        // SHA256 hash should produce consistent output
        $expected = str_replace('=', '', strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'));
        $this->assertEquals($expected, $challenge);
    }

    public function test_generate_authorize_url_with_correct_parameters(): void
    {
        $sut = $this->getSUT();
        $state = 'test_state';
        $challenge = 'test_challenge';
        
        $url = $sut->generateAuthorizeUrl($state, $challenge);

        $this->assertStringContainsString('https://twitter.com/i/oauth2/authorize', $url);
        $this->assertStringContainsString('response_type=code', $url);
        $this->assertStringContainsString('client_id=test_client_id', $url);
        $this->assertStringContainsString('redirect_uri=https%3A%2F%2Fexample.com%2Fcallback', $url);
        $this->assertStringContainsString('state=test_state', $url);
        $this->assertStringContainsString('code_challenge=test_challenge', $url);
        $this->assertStringContainsString('code_challenge_method=S256', $url);
        $this->assertStringContainsString('scope=tweet.read%20tweet.write%20users.read%20offline.access', $url);
    }

    public function test_verify_state_success(): void
    {
        $sut = $this->getSUT();
        $expected = 'test_state_123';
        $actual = 'test_state_123';

        // Should not throw exception
        $sut->verifyState($expected, $actual);
        $this->assertTrue(true); // If we reach here, verification passed
    }

    public function test_verify_state_throws_exception_on_mismatch(): void
    {
        $this->expectException(InvalidStateException::class);
        $this->expectExceptionMessage('state mismach!');

        $sut = $this->getSUT();
        $expected = 'expected_state';
        $actual = 'different_state';

        $sut->verifyState($expected, $actual);
    }

    public function test_generate_token_success(): void
    {
        $now = Carbon::parse('2024-01-01 00:00:00');
        $code = 'authorization_code_123';
        $codeVerifier = 'code_verifier_123';

        $responseData = [
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write users.read offline.access',
            'access_token' => 'access_token_abc',
            'refresh_token' => 'refresh_token_xyz',
            'expires_in' => 7200,
        ];

        $mockClient = $this->mock(Client::class, function (MockInterface $mock) use ($responseData, $code, $codeVerifier): void {
            $mock->expects('request')
                ->once()
                ->with(
                    'POST',
                    'https://api.twitter.com/2/oauth2/token',
                    [
                        'auth' => ['test_client_id', 'test_client_secret'],
                        'form_params' => [
                            'code' => $code,
                            'grant_type' => 'authorization_code',
                            'redirect_uri' => 'https://example.com/callback',
                            'code_verifier' => $codeVerifier,
                        ],
                    ]
                )
                ->andReturn(new Response(200, [], json_encode($responseData)));
        });

        $expectedToken = new OauthToken([
            'application' => 'twitter',
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write users.read offline.access',
            'access_token' => 'access_token_abc',
            'refresh_token' => 'refresh_token_xyz',
            'expired_at' => $now->copy()->addSeconds(7200),
        ]);

        $mockRepository = $this->mock(OauthTokenRepository::class, function (MockInterface $mock) use ($expectedToken): void {
            $mock->expects('updateOrCreate')
                ->once()
                ->with(
                    ['application' => 'twitter'],
                    [
                        'token_type' => 'bearer',
                        'scope' => 'tweet.read tweet.write users.read offline.access',
                        'access_token' => 'access_token_abc',
                        'refresh_token' => 'refresh_token_xyz',
                        'expired_at' => \Mockery::type(Carbon::class),
                    ]
                )
                ->andReturn($expectedToken);
        });

        $sut = $this->getSUT($now, $mockClient, $mockRepository);
        $result = $sut->generateToken($code, $codeVerifier);

        $this->assertInstanceOf(OauthToken::class, $result);
        $this->assertEquals('twitter', $result->application);
        $this->assertEquals('access_token_abc', $result->access_token);
        $this->assertEquals('refresh_token_xyz', $result->refresh_token);
    }

    public function test_refresh_token_success(): void
    {
        $now = Carbon::parse('2024-01-01 00:00:00');

        $existingToken = new OauthToken([
            'application' => 'twitter',
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write users.read offline.access',
            'access_token' => 'old_access_token',
            'refresh_token' => 'old_refresh_token',
            'expired_at' => $now->copy()->subHour(),
        ]);

        $responseData = [
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write users.read offline.access',
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expires_in' => 7200,
        ];

        $mockClient = $this->mock(Client::class, function (MockInterface $mock) use ($responseData): void {
            $mock->expects('request')
                ->once()
                ->with(
                    'POST',
                    'https://api.twitter.com/2/oauth2/token',
                    [
                        'auth' => ['test_client_id', 'test_client_secret'],
                        'form_params' => [
                            'refresh_token' => 'old_refresh_token',
                            'grant_type' => 'refresh_token',
                        ],
                    ]
                )
                ->andReturn(new Response(200, [], json_encode($responseData)));
        });

        $refreshedToken = new OauthToken([
            'application' => 'twitter',
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write users.read offline.access',
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'expired_at' => $now->copy()->addSeconds(7200),
        ]);

        $mockRepository = $this->mock(OauthTokenRepository::class, function (MockInterface $mock) use ($refreshedToken): void {
            $mock->expects('updateOrCreate')
                ->once()
                ->with(
                    ['application' => 'twitter'],
                    [
                        'token_type' => 'bearer',
                        'scope' => 'tweet.read tweet.write users.read offline.access',
                        'access_token' => 'new_access_token',
                        'refresh_token' => 'new_refresh_token',
                        'expired_at' => \Mockery::type(Carbon::class),
                    ]
                )
                ->andReturn($refreshedToken);
        });

        $sut = $this->getSUT($now, $mockClient, $mockRepository);
        $result = $sut->refreshToken($existingToken);

        $this->assertInstanceOf(OauthToken::class, $result);
        $this->assertEquals('new_access_token', $result->access_token);
        $this->assertEquals('new_refresh_token', $result->refresh_token);
    }

    public function test_refresh_token_failure_throws_exception(): void
    {
        $this->expectException(\GuzzleHttp\Exception\ClientException::class);

        $now = Carbon::parse('2024-01-01 00:00:00');

        $existingToken = new OauthToken([
            'application' => 'twitter',
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write users.read offline.access',
            'access_token' => 'old_access_token',
            'refresh_token' => 'invalid_refresh_token',
            'expired_at' => $now->copy()->subHour(),
        ]);

        $mockClient = $this->mock(Client::class, function (MockInterface $mock): void {
            $mock->expects('request')
                ->once()
                ->andThrow(new \GuzzleHttp\Exception\ClientException(
                    'Invalid refresh token',
                    new \GuzzleHttp\Psr7\Request('POST', 'test'),
                    new Response(401, [], json_encode(['error' => 'invalid_grant']))
                ));
        });

        $mockRepository = $this->mock(OauthTokenRepository::class);

        $sut = $this->getSUT($now, $mockClient, $mockRepository);
        $sut->refreshToken($existingToken);
    }

    public function test_revoke_token_success(): void
    {
        $token = new OauthToken([
            'application' => 'twitter',
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write users.read offline.access',
            'access_token' => 'access_token_to_revoke',
            'refresh_token' => 'refresh_token_123',
            'expired_at' => Carbon::now()->addHour(),
        ]);

        $mockClient = $this->mock(Client::class, function (MockInterface $mock): void {
            $mock->expects('request')
                ->once()
                ->with(
                    'POST',
                    'https://api.twitter.com/2/oauth2/revoke',
                    [
                        'auth' => ['test_client_id', 'test_client_secret'],
                        'form_params' => [
                            'token' => 'access_token_to_revoke',
                            'token_type_hint' => 'access_token',
                        ],
                    ]
                )
                ->andReturn(new Response(200));
        });

        $mockRepository = $this->mock(OauthTokenRepository::class, function (MockInterface $mock) use ($token): void {
            $mock->expects('delete')
                ->once()
                ->with($token);
        });

        $sut = $this->getSUT(null, $mockClient, $mockRepository);
        $sut->revokeToken($token);

        // If we reach here without exception, the test passes
        $this->assertTrue(true);
    }

    public function test_revoke_token_handles_api_failure_gracefully(): void
    {
        $token = new OauthToken([
            'application' => 'twitter',
            'token_type' => 'bearer',
            'scope' => 'tweet.read tweet.write users.read offline.access',
            'access_token' => 'access_token_to_revoke',
            'refresh_token' => 'refresh_token_123',
            'expired_at' => Carbon::now()->addHour(),
        ]);

        $mockClient = $this->mock(Client::class, function (MockInterface $mock): void {
            $mock->expects('request')
                ->once()
                ->andThrow(new \Exception('API error'));
        });

        $mockRepository = $this->mock(OauthTokenRepository::class, function (MockInterface $mock) use ($token): void {
            // Even if API call fails, the token should still be deleted locally
            $mock->expects('delete')
                ->once()
                ->with($token);
        });

        $sut = $this->getSUT(null, $mockClient, $mockRepository);
        $sut->revokeToken($token);

        // Should not throw exception - gracefully handles API failure
        $this->assertTrue(true);
    }
}
