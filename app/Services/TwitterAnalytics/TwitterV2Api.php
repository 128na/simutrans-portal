<?php

namespace App\Services\TwitterAnalytics;

use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterV2Api extends TwitterOAuth
{
    public const PKCE_TOKEN = 'pkce_token';
    public const APP_ONLY_TOKEN = 'app_only_token';

    public function __construct(
        string $consumerKey,
        string $consumerSecret,
        ?string $oauthToken = null,
        ?string $oauthTokenSecret = null,
        private string $tokenType = self::APP_ONLY_TOKEN
    ) {
        parent::__construct(
            $consumerKey,
            $consumerSecret,
            $oauthToken,
            $oauthTokenSecret
        );
    }

    public function isPkceToken(): bool
    {
        return $this->tokenType === self::PKCE_TOKEN;
    }
}
