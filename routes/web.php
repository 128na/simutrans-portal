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

// 認証
Auth::routes(['verify' => true]);

// 非ログイン系
Route::get('/', 'FrontController@index')->name('index');
Route::get('/addons', 'ArticleController@addons')->name('addons.index');
Route::get('/pages', 'ArticleController@pages')->name('pages.index');
Route::get('/announces', 'ArticleController@announces')->name('announces.index');
Route::get('/articles/{article}', 'ArticleController@show')->name('articles.show');
Route::get('/articles/{article}/download', 'ArticleController@download')->name('articles.download');
Route::get('/search', 'ArticleController@search')->name('search');
Route::get('/category/pak/{size}/{slug}', 'ArticleController@categoryPakAddon')->name('category.pak.addon');
Route::get('/category/{type}/{slug}', 'ArticleController@category')->name('category');
Route::get('/tag/{tag}', 'ArticleController@tag')->name('tag');
Route::get('/user/{user}', 'ArticleController@user')->name('user');

// ログイン系：ユーザー
Route::prefix('mypage')->group(function () {
    Route::name('mypage.')->group(function () {
        Route::middleware(['auth'])->group(function () {
            Route::get('/', 'Mypage\FrontController@index')->name('index');

            Route::middleware(['verified'])->group(function () {
                Route::get('profile', 'Mypage\ProfileController@edit')->name('profile.edit');
                Route::post('profile', 'Mypage\ProfileController@update')->name('profile.update');

                Route::get('/articles/create/{type}', 'Mypage\ArticleController@create')->name('articles.create');
                Route::post('/articles/create/addon-post/{preview?}', 'Mypage\AddonPostController@store')->name('articles.store.addon-post');
                Route::post('/articles/create/addon-introduction/{preview?}', 'Mypage\AddonIntroductionController@store')->name('articles.store.addon-introduction');
                Route::post('/articles/create/page/{preview?}', 'Mypage\PageController@store')->name('articles.store.page');

                Route::get('/articles/edit/{article}', 'Mypage\ArticleController@edit')->name('articles.edit');
                Route::post('/articles/edit/addon-post/{article}/{preview?}', 'Mypage\AddonPostController@update')->name('articles.update.addon-post');
                Route::post('/articles/edit/addon-introduction/{article}/{preview?}', 'Mypage\AddonIntroductionController@update')->name('articles.update.addon-introduction');
                Route::post('/articles/edit/page/{article}/{preview?}', 'Mypage\PageController@update')->name('articles.update.page');
            });
        });
    });
});

// ログイン系：管理者
Route::prefix('admin')->group(function () {
    Route::name('admin.')->group(function () {
        Route::middleware(['auth', 'admin', 'verified'])->group(function () {
            Route::get('/', 'Admin\FrontController@index')->name('index');

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
