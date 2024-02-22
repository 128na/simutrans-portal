<?php

declare(strict_types=1);

namespace App\Services\Twitter;

use App\Models\OauthToken;
use App\Repositories\OauthTokenRepository;
use App\Services\Twitter\Exceptions\InvalidStateException;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Throwable;

class PKCEService
{
    public function __construct(
        private readonly Carbon $now,
        private readonly Client $client,
        private readonly OauthTokenRepository $oauthTokenRepository,
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $callbackUrl,
    ) {
    }

    public function generateState(int $length = 32): string
    {
        return Str::random($length);
    }

    /**
     * @see https://qiita.com/sugamaan/items/50699432a65ad9e5829e
     */
    public function generateCodeVerifier(int $byteLength = 32): string
    {
        return str_replace('=', '', strtr(base64_encode(openssl_random_pseudo_bytes($byteLength)), '+/', '-_'));
    }

    public function generateCodeChallenge(string $codeVerifier): string
    {
        $hash = hash('sha256', $codeVerifier, true);

        return str_replace('=', '', strtr(base64_encode($hash), '+/', '-_'));
    }

    /**
     * @see https://developer.twitter.com/en/docs/authentication/oauth-2-0/user-access-token
     * @see https://zenn.dev/senk/articles/1eaf89b5a26426
     */
    public function generateAuthorizeUrl(string $state, string $codeChallange): string
    {
        return 'https://twitter.com/i/oauth2/authorize?'.http_build_query([
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->callbackUrl,
            'scope' => 'tweet.read tweet.write users.read offline.access',
            'state' => $state,
            'code_challenge' => $codeChallange,
            'code_challenge_method' => 'S256',
        ], '', null, PHP_QUERY_RFC3986);
    }

    public function verifyState(string $expected, string $actual): void
    {
        if ($expected !== $actual) {
            throw new InvalidStateException('state mismach!');
        }
    }

    public function generateToken(string $code, string $codeVerifier): OauthToken
    {
        $response = $this->client->request('POST', 'https://api.twitter.com/2/oauth2/token', [
            'auth' => [$this->clientId, $this->clientSecret],
            'form_params' => [
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $this->callbackUrl,
                'code_verifier' => $codeVerifier,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        logger('generateToken::data', $data);

        return $this->oauthTokenRepository->updateOrCreate(
            ['application' => 'twitter'],
            [
                'token_type' => $data['token_type'],
                'scope' => $data['scope'],
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expired_at' => $this->now->addSeconds($data['expires_in']),
            ]
        );
    }

    public function refreshToken(OauthToken $oauthToken): OauthToken
    {
        $response = $this->client->request('POST', 'https://api.twitter.com/2/oauth2/token', [
            'auth' => [$this->clientId, $this->clientSecret],
            'form_params' => [
                'refresh_token' => $oauthToken->refresh_token,
                'grant_type' => 'refresh_token',
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        $oauthToken = $this->oauthTokenRepository->updateOrCreate(
            ['application' => 'twitter'],
            [
                'token_type' => $data['token_type'],
                'scope' => $data['scope'],
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expired_at' => $this->now->addSeconds($data['expires_in']),
            ]
        );

        return $oauthToken;
    }

    public function revokeToken(OauthToken $oauthToken): void
    {
        try {
            $this->client->request('POST', 'https://api.twitter.com/2/oauth2/revoke', [
                'auth' => [$this->clientId, $this->clientSecret],
                'form_params' => [
                    'token' => $oauthToken->access_token,
                    'token_type_hint' => 'access_token',
                ],
            ]);
        } catch (Throwable) {
        }

        $this->oauthTokenRepository->delete($oauthToken);
    }
}
