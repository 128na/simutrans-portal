<?php

declare(strict_types=1);

namespace App\Providers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Repositories\Attachment\FileInfoRepository;
use App\Services\BlueSky\BlueSkyApiClient;
use App\Services\BlueSky\ResizeByFileSize;
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
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use potibm\Bluesky\BlueskyApi;
use potibm\Bluesky\BlueskyPostService;
use ZipArchive;

final class DIServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * @return array<string>
     */
    #[\Override]
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
     */
    #[\Override]
    public function register(): void
    {
        $this->app->bind(ReadmeExtractor::class, function ($app): ReadmeExtractor {
            $htmlPurifierConfig = HTMLPurifier_Config::createDefault();
            $htmlPurifierConfig->set('HTML.AllowedElements', []);

            return new ReadmeExtractor(new HTMLPurifier($htmlPurifierConfig));
        });

        $this->app->bind(MarkdownService::class, function ($app): MarkdownService {
            $htmlPurifierConfig = HTMLPurifier_Config::createDefault();
            $htmlPurifierConfig->set('HTML.AllowedElements', []);

            return new MarkdownService($app->make(GithubMarkdown::class), new HTMLPurifier($htmlPurifierConfig));
        });

        $this->app->bind(
            ZipManager::class,
            fn($app): ZipManager => new ZipManager(new ZipArchive(), Storage::disk('public'), [
                $app->make(AddonPostDecorator::class),
                $app->make(AddonIntroductionDecorator::class),
            ]),
        );

        $this->app->bind(
            FileInfoService::class,
            fn($app): FileInfoService => new FileInfoService(
                $this->app->make(FileInfoRepository::class),
                $this->app->make(ZipArchiveParser::class),
                $this->app->make(TextService::class),
                [
                    $this->app->make(DatExtractor::class),
                    $this->app->make(TabExtractor::class),
                    $this->app->make(PakExtractor::class),
                    $this->app->make(ReadmeExtractor::class),
                ],
            ),
        );

        $this->app->bind(
            TwitterOAuth::class,
            fn($app): TwitterOAuth => new TwitterOAuth(
                Config::string('services.twitter.access_token'),
                Config::string('services.twitter.access_secret'),
                null,
                Config::string('services.twitter.bearer_token'),
            ),
        );

        $this->app->bind(
            MisskeyApiClient::class,
            fn($app): MisskeyApiClient => new MisskeyApiClient(
                Config::string('services.misskey.base_url'),
                Config::string('services.misskey.token'),
            ),
        );

        $this->app->bind(BlueSkyApiClient::class, function ($app): BlueSkyApiClient {
            $blueskyApi = new BlueskyApi(
                Config::string('services.bluesky.user'),
                Config::string('services.bluesky.password'),
            );
            $blueskyPostService = new BlueskyPostService($blueskyApi);

            return new BlueSkyApiClient(
                $blueskyApi,
                $blueskyPostService,
                $this->app->make(MetaOgpService::class),
                $this->app->make(ResizeByFileSize::class),
            );
        });
    }
}
