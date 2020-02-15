<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('sitemap', 'SitemapController@index');
Route::feeds();

// 認証
Auth::routes(['verify' => true]);

// 非ログイン系 reidsキャッシュ有効
Route::middleware('minify')->group(function () {
    Route::middleware('cache.response')->group(function () {
        Route::get('/', 'Front\IndexController@index')->name('index');
        Route::get('/addons', 'Front\ArticleController@addons')->name('addons.index');
        Route::get('/ranking', 'Front\ArticleController@ranking')->name('addons.ranking');
        Route::get('/pages', 'Front\ArticleController@pages')->name('pages.index');
        Route::get('/announces', 'Front\ArticleController@announces')->name('announces.index');
        Route::get('/category/pak/{size}/{slug}', 'Front\ArticleController@categoryPakAddon')->name('category.pak.addon');
        Route::get('/category/{type}/{slug}', 'Front\ArticleController@category')->name('category');
        Route::get('/tag/{tag}', 'Front\ArticleController@tag')->name('tag');
        Route::get('/user/{user}', 'Front\ArticleController@user')->name('user');
    });
    // 非ログイン系 reidsキャッシュ無効
    Route::get('/articles/{article}', 'Front\ArticleController@show')->name('articles.show');
    Route::get('/search', 'Front\ArticleController@search')->name('search');
});
Route::get('/language/{name}', 'Front\IndexController@language')->name('language');
Route::middleware('transaction')->group(function () {
    Route::get('/articles/{article}/download', 'Front\ArticleController@download')->name('articles.download');
});

// ログイン系：ユーザー
Route::prefix('mypage')->group(function () {
    Route::name('mypage.')->group(function () {
        Route::middleware('auth')->group(function () {
            Route::get('/', 'Mypage\IndexController@index')->name('index');

            Route::middleware('verified')->group(function () {
                Route::get('profile', 'Mypage\ProfileController@edit')->name('profile.edit');
                Route::middleware('transaction')->group(function () {
                    Route::post('profile', 'Mypage\ProfileController@update')->name('profile.update');
                });

                Route::get('/articles/create/{type}', 'Mypage\ArticleController@create')->name('articles.create');
                Route::middleware('transaction')->group(function () {
                    Route::post('/articles/create/addon-post/{preview?}', 'Mypage\AddonPostController@store')->name('articles.store.addon-post');
                    Route::post('/articles/create/addon-introduction/{preview?}', 'Mypage\AddonIntroductionController@store')->name('articles.store.addon-introduction');
                    Route::post('/articles/create/page/{preview?}', 'Mypage\PageController@store')->name('articles.store.page');
                    Route::post('/articles/create/markdown/{preview?}', 'Mypage\MarkdownController@store')->name('articles.store.markdown');
                });

                Route::middleware('can:update,article')->group(function () {
                    Route::get('/articles/edit/{article}', 'Mypage\ArticleController@edit')->name('articles.edit');
                    Route::middleware('transaction')->group(function () {
                        Route::post('/articles/edit/addon-post/{article}/{preview?}', 'Mypage\AddonPostController@update')->name('articles.update.addon-post');
                        Route::post('/articles/edit/addon-introduction/{article}/{preview?}', 'Mypage\AddonIntroductionController@update')->name('articles.update.addon-introduction');
                        Route::post('/articles/edit/page/{article}/{preview?}', 'Mypage\PageController@update')->name('articles.update.page');
                        Route::post('/articles/edit/markdown/{article}/{preview?}', 'Mypage\MarkdownController@update')->name('articles.update.markdown');
                    });
                });

                Route::get('/articles/analytics', 'Mypage\ArticleController@analytics')->name('articles.analytics');
            });
        });
    });
});

// ログイン系：管理者
Route::prefix('admin')->group(function () {
    Route::name('admin.')->group(function () {
        Route::middleware(['auth', 'admin', 'verified'])->group(function () {
            Route::get('/', 'Admin\IndexController@index')->name('index');

            // デバッグツール
            Route::post('/flush-cache', 'Admin\IndexController@flushCache')->name('flush.cache');
            Route::get('/error', 'Admin\IndexController@error')->name('error');
            Route::get('/warning', 'Admin\IndexController@warning')->name('warning');
            Route::get('/notice', 'Admin\IndexController@notice')->name('notice');

            Route::get('/users', 'Admin\UserController@index')->name('users.index');
            // Route::get('/users/create', 'Admin\UserController@create')->name('users.create');
            // Route::post('/users/create', 'Admin\UserController@store')->name('users.store');
            // Route::get('/users/{user}/edit', 'Admin\UserController@edit')->name('users.edit');
            // Route::post('/users/{user}', 'Admin\UserController@update')->name('users.update');
            // Route::delete('/users/{user}', 'Admin\UserController@destroy')->name('users.destroy');

            Route::get('/articles', 'Admin\ArticleController@index')->name('articles.index');
            // Route::get('/articles/create/announce', 'Admin\AnnounceController@create')->name('articles.create');
            // Route::post('/articles/create/announce', 'Admin\AnnounceController@store')->name('articles.store.announce');

            // Route::get('/categories', 'Admin\CategoryController@index')->name('categories.index');
            // Route::get('/categories/create', 'Admin\CategoryController@create')->name('categories.create');
            // Route::post('/categories/create', 'Admin\CategoryController@store')->name('categories.store');
            // Route::get('/categories/{category}/edit', 'Admin\CategoryController@edit')->name('categories.edit');
            // Route::post('/categories/{category}', 'Admin\CategoryController@update')->name('categories.update');
            // Route::delete('/categories/{category}', 'Admin\CategoryController@destroy')->name('categories.destroy');

            // Route::get('/tags', 'Admin\TagController@index')->name('tags.index');
            // Route::delete('/tags/{tag}', 'Admin\TagController@destroy')->name('tags.destroy');
        });
    });
});

Route::get('{any}', 'RedirectController@index')->where('any', '.*');
