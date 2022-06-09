<?php

namespace App\Services\TwitterAnalytics;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\OauthToken;
use App\Repositories\OauthTokenRepository;
use App\Services\TwitterAnalytics\Exceptions\PKCETokenNotFoundException;
use App\Services\TwitterAnalytics\Exceptions\PKCETokenRefreshFailedException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TwitterV2Api extends TwitterOAuth
{
    private bool $pkceToken = false;

    public function __construct(
        $consumerKey,
        $consumerSecret,
        private $appOnlyBearerToken,
        private OauthTokenRepository $oauthTokenRepository,
        private PKCEService $pKCEService,
    ) {
        parent::__construct(
            $consumerKey,
            $consumerSecret,
            null,
            $appOnlyBearerToken
        );
    }

    public function isPkceToken(): bool
    {
        return $this->pkceToken;
    }

    public function applyPKCEToken(): void
    {
        $token = $this->getPKCEToken();

        $this->setBearer($token->access_token);
        $this->pkceToken = true;
    }

    private function getPKCEToken(): OauthToken
    {
        try {
            $token = $this->oauthTokenRepository->getToken('twitter');

            if (!$token->isExpired()) {
                return $token;
            }
        } catch (ModelNotFoundException $e) {
            throw new PKCETokenNotFoundException();
        }

        logger('token expired, refresh');

        try {
            return $this->pKCEService->refreshToken($token);
        } catch (ClientException $e) {
            try {
                $this->pKCEService->revokeToken($token);
            } finally {
                throw new PKCETokenRefreshFailedException();
            }
        }
    }
}
