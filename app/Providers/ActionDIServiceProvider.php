<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\CreateSitemap\SitemapHandler;
use App\Actions\StoreAttachment\CropImage;
use App\Actions\StoreAttachment\Store;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

final class ActionDIServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[\Override]
    public function register(): void
    {
        $this->app->singleton(SitemapHandler::class, fn (): SitemapHandler => new SitemapHandler(
            Storage::disk('sitemap'),
        ));
        $this->app->singleton(Store::class, fn (Application $app): Store => new Store(
            Storage::disk('public'),
            $app->make(CropImage::class)
        ));
    }
}
