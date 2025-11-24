<?php

declare(strict_types=1);

namespace App\Providers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Repositories\Attachment\FileInfoRepository;
use App\Services\BlueSky\BlueSkyApiClient;
use App\Services\BlueSky\ResizeByFileSize;
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
use Illuminate\Support\ServiceProvider;
use potibm\Bluesky\BlueskyApi;
use potibm\Bluesky\BlueskyPostService;

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
        $this->app->bind(function ($app): \App\Services\FileInfo\Extractors\ReadmeExtractor {
            $htmlPurifierConfig = HTMLPurifier_Config::createDefault();
            $htmlPurifierConfig->set('HTML.AllowedElements', []);

            return new ReadmeExtractor(new HTMLPurifier($htmlPurifierConfig));
        });

        $this->app->bind(function ($app): \App\Services\MarkdownService {
            $htmlPurifierConfig = HTMLPurifier_Config::createDefault();
            $htmlPurifierConfig->set('HTML.AllowedElements', config('services.markdown.allowed_elements', []));

            return new MarkdownService($app->make(GithubMarkdown::class), new HTMLPurifier($htmlPurifierConfig));
        });

        $this->app->bind(FileInfoService::class, fn ($app): FileInfoService => new FileInfoService(
            $this->app->make(FileInfoRepository::class),
            $this->app->make(ZipArchiveParser::class),
            $this->app->make(TextService::class),
            [
                $this->app->make(DatExtractor::class),
                $this->app->make(TabExtractor::class),
                $this->app->make(PakExtractor::class),
                $this->app->make(ReadmeExtractor::class),
            ]
        ));

        $this->app->bind(TwitterOAuth::class, fn ($app): TwitterOAuth => new TwitterOAuth(
            Config::string('services.twitter.access_token'),
            Config::string('services.twitter.access_secret'),
            null,
            Config::string('services.twitter.bearer_token'),
        ));

        $this->app->bind(MisskeyApiClient::class, fn ($app): MisskeyApiClient => new MisskeyApiClient(
            $app->make(\App\Config\EnvironmentConfig::class)->misskeyBaseUrl,
            $app->make(\App\Config\EnvironmentConfig::class)->misskeyToken ?? '',
        ));

        $this->app->bind(function ($app): \App\Services\BlueSky\BlueSkyApiClient {
            $config = $app->make(\App\Config\EnvironmentConfig::class);
            $blueskyApi = new BlueskyApi($config->blueskyUser ?? '', $config->blueskyPassword ?? '');
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
