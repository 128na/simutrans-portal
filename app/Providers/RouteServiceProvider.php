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
    public const HOME = '/';

    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('internal_api')
                ->prefix('api')
                ->group(base_path('routes/internal_api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
        $this->registerRouteBindings();
    }

    private function registerRouteBindings(): void
    {
        Route::bind('invitation_code', function ($value) {
            return User::where('invitation_code', $value)->whereNotNull('email_verified_at')->firstOrFail();
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
        RateLimiter::for('discordInvite', function (Request $request) {
            return [
                Limit::perMinute(1),
                Limit::perHour(10),
                Limit::perDay(50),
            ];
        });
    }
}
