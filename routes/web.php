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

// 認証
Auth::routes();

// 非ログイン系
Route::get('/', 'FrontController@index')->name('index');
// Route::get('/search', 'FrontController@search')->name('search');
// Route::get('/categories/{id}', 'FrontController@categories')->name('categories');
// Route::get('/tags/{id}', 'FrontController@tags')->name('tags');
// Route::get('/users/{id}', 'FrontController@users')->name('users');

// ログイン系：ユーザー
Route::prefix('mypage')->group(function () {
    Route::name('mypage.')->group(function () {
        Route::middleware(['auth'])->group(function () {
            Route::get('/', 'Mypage\FrontController@index')->name('index');
            // Route::get('/edit', 'Mypage\FrontController@edit')->name('edit');
            // Route::post('/edit', 'Mypage\FrontController@update');

            // Route::get('/articles', 'Mypage\ArticleController@index')->name('article.index');
            // Route::get('/articles/create', 'Mypage\ArticleController@create')->name('article.create');
            // Route::post('/articles/create', 'Mypage\ArticleController@store')->name('article.store');
            // Route::get('/articles/{id}/edit', 'Mypage\ArticleController@edit')->name('article.edit');
            // Route::post('/articles/{id}', 'Mypage\ArticleController@update')->name('article.update');
        });
    });
});

// ログイン系：管理者
Route::prefix('admin')->group(function () {
    Route::name('admin.')->group(function () {
        Route::middleware(['auth', 'admin'])->group(function () {
            Route::get('/', 'Admin\FrontController@index')->name('index');

            // Route::get('/users', 'Admin\UserController@index')->name('users.index');
            // Route::get('/users/create', 'Admin\UserController@create')->name('users.create');
            // Route::post('/users/create', 'Admin\UserController@store')->name('users.store');
            // Route::get('/user/{id}/edit', 'Admin\UserController@edit')->name('users.edit');
            // Route::post('/user/{id}', 'Admin\UserController@update')->name('users.update');
            // Route::delete('/user/{id}', 'Admin\UserController@destroy')->name('users.destroy');

            // Route::get('/articles', 'Admin\ArticleController@index')->name('articles.index');
            // Route::delete('/articles/{id}', 'Admin\ArticleController@destroy')->name('articles.destroy');

            // Route::get('/categories', 'Admin\CategoryController@index')->name('categories.index');
            // Route::get('/categories/create', 'Admin\CategoryController@create')->name('categories.create');
            // Route::post('/categories/create', 'Admin\CategoryController@store')->name('categories.store');
            // Route::get('/categories/{id}/edit', 'Admin\CategoryController@edit')->name('categories.edit');
            // Route::post('/categories/{id}', 'Admin\CategoryController@update')->name('categories.update');
            // Route::delete('/categories/{id}', 'Admin\CategoryController@destroy')->name('categories.destroy');

            // Route::get('/tags', 'Admin\TagController@index')->name('tags.index');
            // Route::delete('/tags/{id}', 'Admin\TagController@destroy')->name('tags.destroy');
        });
    });
});
