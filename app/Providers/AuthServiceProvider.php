<?php

namespace App\Providers;

use App\Models\Article;
use App\Policies\ArticlePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Article::class => ArticlePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::hashClientSecrets();
        Passport::tokensCan([
            'user-read' => '自身のプロフィールや投稿データの読み取り',
            'user-write' => '自身のプロフィールや投稿データの更新',
        ]);
        Passport::tokensExpireIn(now()->addDays(14));
    }
}
