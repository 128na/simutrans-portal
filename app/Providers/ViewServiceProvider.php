<?php

namespace App\Providers;

use App\Http\View\Creators\MetaCreator;
use App\Http\View\Creators\SidebarCreator;
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
        $views = [
            'front.index',
            'front.tags',
            'front.articles.index',
            // 'front.articles.show',
        ];
        View::creator($views, SidebarCreator::class);
        View::creator($views, MetaCreator::class);
    }
}
