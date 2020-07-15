<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * 全アプリケーションサービスの登録
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * 全アプリケーションサービスの初期起動
     *
     * @return void
     */
    public function boot()
    {
        View::creator(
            ['front.index', 'front.tags', 'front.articles.index', 'front.articles.show'],
            \App\Http\View\Creators\SidebarCreator::class
        );
        View::creator(
            ['front.index', 'front.tags', 'front.articles.index', 'front.articles.show'],
            \App\Http\View\Creators\MetaCreator::class
        );
    }
}
