<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Discord\InviteService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use RestCord\DiscordClient;

class DiscordServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * @return array<class-string>
     */
    public function provides()
    {
        return [
            InviteService::class,
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(InviteService::class, function () {
            return new InviteService(
                new DiscordClient(['token' => config('services.discord.token')])
            );
        });
    }
}
