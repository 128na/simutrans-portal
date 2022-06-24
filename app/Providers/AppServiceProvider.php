<?php

namespace App\Providers;

use App\Models\User;
use App\Repositories\Attachment\FileInfoRepository;
use App\Repositories\AttachmentRepository;
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
use Carbon\CarbonImmutable;
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

        $this->app->bind(FileInfoService::class, function ($app) {
            return new FileInfoService(
                $this->app->make(AttachmentRepository::class),
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
