<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\CreateSitemap\Create;
use App\Actions\CreateSitemap\Destroy;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

final class ActionDIServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Create::class, function () {
            return new Create(Storage::disk('sitemap'));
        });
        $this->app->singleton(Destroy::class, function () {
            return new Destroy(Storage::disk('sitemap'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
