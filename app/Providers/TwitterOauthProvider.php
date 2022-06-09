<?php

namespace App\Providers;

use App\Repositories\OauthTokenRepository;
use App\Services\TwitterAnalytics\PKCEService;
use App\Services\TwitterAnalytics\TwitterV1Api;
use App\Services\TwitterAnalytics\TwitterV2Api;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class TwitterOauthProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides()
    {
        return [
            PKCEService::class,
            TwitterV1Api::class,
            TwitterV2Api::class,
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PKCEService::class, function () {
            return new PKCEService(
                $this->app->make(Carbon::class),
                $this->app->make(Client::class),
                $this->app->make(OauthTokenRepository::class),
                config('twitter.client_id'),
                config('twitter.client_secret'),
                route('admin.oauth.twitter.callback'),
            );
        });

        $this->app->bind(TwitterV1Api::class, function () {
            return new TwitterV1Api(
                config('twitter.consumer_key'),
                config('twitter.consumer_secret'),
                config('twitter.access_token'),
                config('twitter.access_token_secret')
            );
        });

        $this->app->bind(TwitterV2Api::class, function () {
            $client = new TwitterV2Api(
                config('twitter.consumer_key'),
                config('twitter.consumer_secret'),
                config('twitter.bearer_token'),
                $this->app->make(OauthTokenRepository::class),
                $this->app->make(PKCEService::class),
            );

            $client->setApiVersion('2');

            return $client;
        });
    }
}
