<?php

namespace App\Services\TwitterAnalytics;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class PKCEService
{
    public function __construct(private Client $client)
    {
    }

    public function generateState(): string
    {
        return (string) random_int(1000, 9999);
    }

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
            'client_id' => config('twitter.client_id'),
            'redirect_uri' => route('admin.oauth.twitter.callback'),
            'scope' => 'users.read tweet.read offline.access',
            'state' => $state,
            'code_challenge' => $codeChallange,
            'code_challenge_method' => 'S256',
        ]);
    }

    public function verifyState(string $expected, string $actual): void
    {
        if ($expected !== $actual) {
            throw new \Exception('state mismach!');
        }
    }

    public function generateToken(string $code, string $codeVerifier): array
    {
        $res = $this->client->request('POST', 'https://api.twitter.com/2/oauth2/token', [
            'auth' => [config('twitter.client_id'), config('twitter.client_secret')],
            'form_params' => [
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => route('admin.oauth.twitter.callback'),
                'code_verifier' => $codeVerifier,
            ],
        ]);

        $data = json_decode($res->getBody()->getContents(), true);

        logger('generate token', [$data]);

        return $data;
    }

    public function refreshToken(string $refreshToken): array
    {
        $res = $this->client->request('POST', 'https://api.twitter.com/2/oauth2/token', [
            'auth' => [config('twitter.client_id'), config('twitter.client_secret')],
            'form_params' => [
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
            ],
        ]);

        $data = json_decode($res->getBody()->getContents(), true);

        logger('generate token', [$data]);
        Cache::put('oauth2.twitter.access_token', $data['access_token']);
        Cache::put('oauth2.twitter.refresh_token', $data['refresh_token']);

        return $data;
    }

    public function revokeToken(string $accessToken): void
    {
        $res = $this->client->request('POST', 'https://api.twitter.com/2/oauth2/revoke', [
            'auth' => [config('twitter.client_id'), config('twitter.client_secret')],
            'form_params' => [
                'token' => $accessToken,
            ],
        ]);

        $data = json_decode($res->getBody()->getContents(), true);

        logger('revoke token', [$data]);
        Cache::forget('oauth2.twitter.access_token');
        Cache::forget('oauth2.twitter.refresh_token');
    }

    public function getAccessToken(): ?string
    {
        return Cache::get('oauth2.twitter.access_token');
    }

    public function getRefreshToken(): ?string
    {
        return Cache::get('oauth2.twitter.refresh_token');
    }
}
