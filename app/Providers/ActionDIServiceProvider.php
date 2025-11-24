<?php

declare(strict_types=1);

namespace App\Providers;

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
        $this->app->singleton(Store::class, fn (Application $application): Store => new Store(
            Storage::disk('public')
        ));
    }
}
