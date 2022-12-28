<?php

namespace App\Providers;

use App\Repositories\OauthTokenRepository;
use App\Services\Twitter\PKCEService;
use App\Services\Twitter\TweetService;
use App\Services\Twitter\TwitterV1Api;
use App\Services\Twitter\TwitterV2Api;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class TwitterOauthProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * @return array<class-string>
     */
    public function provides()
    {
        return [
            PKCEService::class,
            TwitterV1Api::class,
            TwitterV2Api::class,
            TweetService::class,
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TweetService::class, function () {
            return new TweetService(
                $this->app->make(TwitterV1Api::class),
                (bool) $this->app->environment(['production']),
            );
        });

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
