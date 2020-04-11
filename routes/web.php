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

// メール確認
Route::GET('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify');
// PWリセット
Route::POST('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
Route::GET('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');

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
    Route::get('/mypage/', 'Mypage\IndexController@index')->name('mypage.index');
    Route::get('/mypage/{any}', 'RedirectController@mypage')->where('any', '.*');
});
Route::get('/language/{name}', 'Front\IndexController@language')->name('language');
Route::get('/articles/{article}/download', 'Front\ArticleController@download')->name('articles.download');

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
