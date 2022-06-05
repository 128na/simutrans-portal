<?php

namespace App\Services\TwitterAnalytics;

use App\Models\OauthToken;
use App\Repositories\OauthTokenRepository;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class PKCEService
{
    public function __construct(
        private Client $client,
        private OauthTokenRepository $oauthTokenRepository,
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
            'client_id' => config('twitter.client_id'),
            'redirect_uri' => route('admin.oauth.twitter.callback'),
            'scope' => 'users.read tweet.read list.read offline.access',
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

    public function generateToken(string $code, string $codeVerifier): OauthToken
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

        $token = $this->oauthTokenRepository->updateOrCreate(
            ['application' => 'twitter'],
            [
                'token_type' => $data['token_type'],
                'scope' => $data['scope'],
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expired_at' => now()->addSeconds($data['expires_in']),
            ]
        );

        return $token;
    }

    public function refreshToken(OauthToken $token): OauthToken
    {
        $res = $this->client->request('POST', 'https://api.twitter.com/2/oauth2/token', [
            'auth' => [config('twitter.client_id'), config('twitter.client_secret')],
            'form_params' => [
                'refresh_token' => $token->refresh_token,
                'grant_type' => 'refresh_token',
            ],
        ]);

        $data = json_decode($res->getBody()->getContents(), true);

        logger('generate token', [$data]);

        $token = $this->oauthTokenRepository->updateOrCreate(
            ['application' => 'twitter'],
            [
                'token_type' => $data['token_type'],
                'scope' => $data['scope'],
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expired_at' => now()->addSeconds($data['expires_in']),
            ]
        );

        return $token;
    }

    public function revokeToken(OauthToken $token): void
    {
        $res = $this->client->request('POST', 'https://api.twitter.com/2/oauth2/revoke', [
            'auth' => [config('twitter.client_id'), config('twitter.client_secret')],
            'form_params' => [
                'token' => $token->access_token,
                'token_type_hint' => 'access_token',
            ],
        ]);

        $data = json_decode($res->getBody()->getContents(), true);

        logger('revoke token', [$data]);

        $this->oauthTokenRepository->delete($token);
    }
}
