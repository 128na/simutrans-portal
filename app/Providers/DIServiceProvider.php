<?php

declare(strict_types=1);

namespace App\Providers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Repositories\Attachment\FileInfoRepository;
use App\Services\BlueSky\BlueSkyApiClient;
use App\Services\BulkZip\Decorators\AddonIntroductionDecorator;
use App\Services\BulkZip\Decorators\AddonPostDecorator;
use App\Services\BulkZip\ZipManager;
use App\Services\FileInfo\Extractors\DatExtractor;
use App\Services\FileInfo\Extractors\PakExtractor;
use App\Services\FileInfo\Extractors\ReadmeExtractor;
use App\Services\FileInfo\Extractors\TabExtractor;
use App\Services\FileInfo\FileInfoService;
use App\Services\FileInfo\TextService;
use App\Services\FileInfo\ZipArchiveParser;
use App\Services\Front\MetaOgpService;
use App\Services\MarkdownService;
use App\Services\Misskey\MisskeyApiClient;
use cebe\markdown\GithubMarkdown;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use potibm\Bluesky\BlueskyApi;
use potibm\Bluesky\BlueskyPostService;
use ZipArchive;

class DIServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * @return array<string>
     */
    public function provides()
    {
        return [
            ReadmeExtractor::class,
            MarkdownService::class,
            ZipManager::class,
            FileInfoService::class,
            TwitterOAuth::class,
            MisskeyApiClient::class,
            BlueSkyApiClient::class,
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ReadmeExtractor::class, function ($app) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('HTML.AllowedElements', []);

            return new ReadmeExtractor(new HTMLPurifier($config));
        });

        $this->app->bind(MarkdownService::class, function ($app) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('HTML.AllowedElements', []);

            return new MarkdownService($app->make(GithubMarkdown::class), new HTMLPurifier($config));
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

        $this->app->bind(FileInfoService::class, function ($app) {
            return new FileInfoService(
                $this->app->make(FileInfoRepository::class),
                $this->app->make(ZipArchiveParser::class),
                $this->app->make(TextService::class),
                [
                    $this->app->make(DatExtractor::class),
                    $this->app->make(TabExtractor::class),
                    $this->app->make(PakExtractor::class),
                    $this->app->make(ReadmeExtractor::class),
                ]
            );
        });

        $this->app->bind(TwitterOAuth::class, function ($app) {
            return new TwitterOAuth(
                config('services.twitter.access_token'),
                config('services.twitter.access_secret'),
                null,
                config('services.twitter.bearer_token'),
            );
        });

        $this->app->bind(MisskeyApiClient::class, function ($app) {
            return new MisskeyApiClient(
                config('services.misskey.base_url'),
                config('services.misskey.token'),
            );
        });

        $this->app->bind(BlueSkyApiClient::class, function ($app) {
            $api = new BlueskyApi(config('services.bluesky.user'), config('services.bluesky.password'));
            $service = new BlueskyPostService($api);

            return new BlueSkyApiClient($api, $service, $this->app->make(MetaOgpService::class));
        });
    }
}
