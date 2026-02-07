<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const string HOME = '/';

    #[\Override]
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function (): void {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
            Route::middleware('internal_api')
                ->prefix('api')
                ->group(base_path('routes/internal_api.php'));
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
            Route::middleware('web')
                ->group(base_path('routes/ai.php'));
        });
        $this->registerRouteBindings();
    }

    private function registerRouteBindings(): void
    {
        Route::bind('invitation_code', fn ($value) => User::where('invitation_code', $value)->whereNotNull('email_verified_at')->firstOrFail());
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('register', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
        RateLimiter::for('discordInvite', fn (): array => [
            Limit::perMinute(1),
            Limit::perHour(10),
            Limit::perDay(50),
        ]);

        RateLimiter::for('external', fn (Request $request) => Limit::perMinute(10));
    }
}
