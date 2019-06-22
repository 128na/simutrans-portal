<?php

namespace App\Providers;

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
        //
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
    }
}
