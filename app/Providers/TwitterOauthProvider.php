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

final class TwitterOauthProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * @return array<class-string>
     */
    #[\Override]
    public function provides()
    {
        return [
            PKCEService::class,
            TwitterV2Api::class,
        ];
    }

    /**
     * Register any application services.
     */
    #[\Override]
    public function register(): void
    {
        $this->app->bind(PKCEService::class, fn (): \App\Services\Twitter\PKCEService => new PKCEService(
            $this->app->make(Carbon::class),
            $this->app->make(Client::class),
            $this->app->make(OauthTokenRepository::class),
            ($config = $this->app->make(\App\Config\EnvironmentConfig::class))->twitterClientId ?? '',
            $config->twitterClientSecret ?? '',
            route('admin.oauth.twitter.callback'),
        ));

        $this->app->bind(function (): \App\Services\Twitter\TwitterV2Api {
            $environmentConfig = $this->app->make(\App\Config\EnvironmentConfig::class);
            $twitterV2Api = new TwitterV2Api(
                $environmentConfig->twitterConsumerKey ?? '',
                $environmentConfig->twitterConsumerSecret ?? '',
                $environmentConfig->twitterBearerToken ?? '',
                $this->app->make(OauthTokenRepository::class),
                $this->app->make(PKCEService::class),
            );
            $twitterV2Api->applyPKCEToken();

            return $twitterV2Api;
        });
    }
}
