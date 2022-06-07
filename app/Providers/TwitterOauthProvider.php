<?php

namespace App\Providers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\OauthToken;
use App\Repositories\OauthTokenRepository;
use App\Services\TwitterAnalytics\PKCEService;
use App\Services\TwitterAnalytics\TwitterV2Api;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class TwitterOauthProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides()
    {
        return [
            TwitterOAuth::class,
            TwitterV2Api::class,
            PKCEService::class,
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PKCEService::class, function (App $app) {
            return new PKCEService(
                $app->make(Carbon::class),
                $app->make(Client::class),
                $app->make(OauthTokenRepository::class),
                config('twitter.client_id'),
                config('twitter.client_secret'),
                route('admin.oauth.twitter.callback'),
            );
        });

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

            /** @var PKCEService */
            $service = app(PKCEService::class);
            try {
                $service->refreshToken($token);
            } catch (\Throwable $e) {
                logger()->error('refresh failed, revoke token');
                $service->revokeToken($token);
            }
        }

        return $token;
    }
}
