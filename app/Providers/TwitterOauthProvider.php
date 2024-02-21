<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\OauthTokenRepository;
use App\Services\Twitter\PKCEService;
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
        $this->app->bind(PKCEService::class, fn () => new PKCEService(
            $this->app->make(Carbon::class),
            $this->app->make(Client::class),
            $this->app->make(OauthTokenRepository::class),
            config('services.twitter.client_id'),
            config('services.twitter.client_secret'),
            route('admin.oauth.twitter.callback'),
        ));

        $this->app->bind(TwitterV2Api::class, function () {
            $client = new TwitterV2Api(
                config('services.twitter.consumer_key'),
                config('services.twitter.consumer_secret'),
                config('services.twitter.bearer_token'),
                $this->app->make(OauthTokenRepository::class),
                $this->app->make(PKCEService::class),
            );
            $client->applyPKCEToken();

            return $client;
        });
    }
}
