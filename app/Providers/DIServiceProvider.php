<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Attachment\FileInfoRepository;
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
use App\Services\MarkdownService;
use cebe\markdown\GithubMarkdown;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
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
    }
}
