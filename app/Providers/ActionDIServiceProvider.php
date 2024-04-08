<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\CreateSitemap\SitemapHandler;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

final class ActionDIServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SitemapHandler::class, function () {
            return new SitemapHandler(
                Storage::disk('sitemap'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
