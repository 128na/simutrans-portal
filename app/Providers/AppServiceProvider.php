<?php

namespace App\Providers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\User;
use App\Services\BulkZip\Decorators\AddonIntroductionDecorator;
use App\Services\BulkZip\Decorators\AddonPostDecorator;
use App\Services\BulkZip\ZipManager;
use App\Services\MarkdownService;
use Carbon\CarbonImmutable;
use Google\Service\AnalyticsReporting;
use Google\Service\AnalyticsReporting\Resource\Reports;
use Google_Client;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Storage;
use ZipArchive;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TwitterOAuth::class, function ($app) {
            return new TwitterOAuth(
                config('twitter.consumer_key'),
                config('twitter.consumer_secret'),
                config('twitter.access_token'),
                config('twitter.access_token_secret')
            );
        });

        $this->app->bind(HTMLPurifier::class, function ($app) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('HTML.AllowedElements', [
                'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                'hr',
                'pre', 'code',
                'blockquote',
                'table', 'tr', 'td', 'th', 'thead', 'tbody',
                'strong', 'em', 'b', 'i', 'u', 's', 'span',
                'a', 'p', 'br',
                'ul', 'ol', 'li',
                'img',
            ]);

            return new HTMLPurifier($config);
        });

        $this->app->bind(ZipManager::class, function ($app) {
            return new ZipManager(
                new ZipArchive(),
                Storage::disk('public'),
                [
                    $app->make(AddonPostDecorator::class),
                    $app->make(AddonIntroductionDecorator::class),
                ]
            );
        });

        $this->app->bind(Reports::class, function ($app) {
            $client = $app->make(Google_Client::class);
            $client->setApplicationName(config('app.name'));
            $client->setAuthConfig(config('analytics.key_location'));
            $client->setScopes(config('analytics.scopes'));

            $analyticsReporting = $app->make(AnalyticsReporting::class, ['clientOrConfig' => $client]);

            return $analyticsReporting->reports;
        });
    }

    private function registerRouteBindings()
    {
        Route::bind('invitation_code', function ($value) {
            return User::where('invitation_code', $value)->whereNotNull('email_verified_at')->firstOrFail();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // blade内の改行を有効にする
        // https://codeday.me/jp/qa/20190208/214590.html
        \Blade::setEchoFormat('nl2br(e(%s), false)');

        Date::use(CarbonImmutable::class);

        MarkdownService::registerBlade();

        $this->registerRouteBindings();
    }
}
