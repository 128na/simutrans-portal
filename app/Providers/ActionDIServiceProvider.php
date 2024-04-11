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
    #[\Override]
    public function register(): void
    {
        $this->app->singleton(SitemapHandler::class, fn (): \App\Actions\CreateSitemap\SitemapHandler => new SitemapHandler(
            Storage::disk('sitemap'),
        ));
    }
}
