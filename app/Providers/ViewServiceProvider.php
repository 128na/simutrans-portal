<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * 全アプリケーションサービスの登録.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * 全アプリケーションサービスの初期起動.
     *
     * @return void
     */
    public function boot()
    {
        $views = ['front.index', 'front.tags', 'front.articles.index', 'front.articles.show', 'front.articles.advancedSearch'];
        View::creator($views, \App\Http\View\Creators\SidebarCreator::class);
        View::creator($views, \App\Http\View\Creators\MetaCreator::class);
    }
}
