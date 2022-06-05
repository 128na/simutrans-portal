<?php

namespace App\Providers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\OauthToken;
use App\Services\TwitterAnalytics\PKCEService;
use App\Services\TwitterAnalytics\TwitterV2Api;
use Illuminate\Database\QueryException;
use Illuminate\Support\ServiceProvider;

class TwitterOauthProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TwitterOAuth::class, function () {
            return new TwitterOAuth(
                config('twitter.consumer_key'),
                config('twitter.consumer_secret'),
                config('twitter.access_token'),
                config('twitter.access_token_secret')
            );
        });

        $this->app->bind(TwitterV2Api::class, function () {
            // bearer token https://github.com/abraham/twitteroauth/issues/431
            try {
                $token = OauthToken::where('application', 'twitter')->first();
            } catch (QueryException $e) {
                $token = null;
            }
            if ($token) {
                $token = $this->updateTokenIfERxpired($token);
                $client = new TwitterV2Api(
                    config('twitter.consumer_key'),
                    config('twitter.consumer_secret'),
                    null,
                    $token->access_token,
                    TwitterV2Api::PKCE_TOKEN,
                );
            } else {
                $client = new TwitterV2Api(
                    config('twitter.consumer_key'),
                    config('twitter.consumer_secret'),
                    null,
                    config('twitter.bearer_token'),
                    TwitterV2Api::APP_ONLY_TOKEN,
                );
            }

            $client->setApiVersion('2');

            return $client;
        });
    }

    private function updateTokenIfERxpired(OauthToken $token): OauthToken
    {
        if ($token->isExpired()) {
            logger('token expired, refresh');

            return app(PKCEService::class)->refreshToken($token);
        }

        return $token;
    }
}
