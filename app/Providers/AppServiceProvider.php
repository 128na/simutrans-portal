<?php

namespace App\Providers;

use App\Services\MarkdownService;
use Carbon\CarbonImmutable;
use FullTextSearchQueryLike;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FullTextSearchQueryLike::class, function ($app) {
            return new FullTextSearchQueryLike(['title', 'contents']);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // blade内の改行を有効にする
        // https://codeday.me/jp/qa/20190208/214590.html
        \Blade::setEchoFormat('nl2br(e(%s), false)');

        Date::use(CarbonImmutable::class);

        MarkdownService::registerBlade();
    }
}
