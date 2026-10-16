<?php

declare(strict_types=1);

namespace App\Providers;

use App\Auth\HostSessionGuard;
use App\Models\Article;
use App\Policies\ArticlePolicy;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

// use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        Article::class => ArticlePolicy::class,
    ];

    public function boot(): void
    {
        Auth::extend('host-session', function (Application $app, string $name, array $config): HostSessionGuard {
            $provider = Auth::createUserProvider($config['provider'] ?? null);
            if ($provider === null) {
                throw new \InvalidArgumentException('The host session guard requires a user provider.');
            }

            $guard = new HostSessionGuard(
                $name,
                $provider,
                $app['session.store'],
                rehashOnLogin: $app['config']->get('hashing.rehash_on_login', true),
                timeboxDuration: $app['config']->get('auth.timebox_duration', 200000),
                hashKey: $app['config']->get('app.key'),
            );
            $guard->setCookieJar($app['cookie']);
            $guard->setDispatcher($app['events']);
            $guard->setRequest($app->refresh('request', $guard, 'setRequest'));

            if (isset($config['remember'])) {
                $guard->setRememberDuration($config['remember']);
            }

            return $guard;
        });
    }
}
