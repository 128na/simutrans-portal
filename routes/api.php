<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
Route::prefix('v1')->name('api.v1.')->namespace('Api\v1')->group(function () {
    Route::post('click/{article}', 'ConversionController@click');
});

// auth
Route::prefix('v2')->name('api.v2.')->group(function () {
    // 登録
    Route::POST('register', 'Auth\RegisterController@registerApi')->name('register');
    // メール確認
    Route::POST('email/resend', 'Auth\VerificationController@resendApi')->name('verification.resend');
    // 認証
    Route::POST('login', 'Auth\LoginController@login')->name('login');
    Route::POST('logout', 'Auth\LoginController@logout')->name('logout');
    // PWリセット
    Route::POST('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
});

Route::prefix('v2')->name('api.v2.')->namespace('Api\v2')->group(function () {
    // 一般公開API
    Route::get('articles/latest', 'ArticleController@latest')->name('articles.latest');
    Route::get('articles/search', 'ArticleController@search')->name('articles.search');
    Route::get('articles/user/{user}', 'ArticleController@user')->name('articles.user');
    Route::get('articles/tag/{tag}', 'ArticleController@tag')->name('articles.tag');
    Route::get('articles/category/{category}', 'ArticleController@category')->name('articles.category');

    // マイページ機能
    Route::prefix('mypage')->namespace('Mypage')->middleware(['auth'])->group(function () {
        Route::get('user', 'UserController@index')->name('users.index');
        Route::get('tags', 'TagController@search')->name('tags.search');
        Route::get('attachments', 'AttachmentController@index')->name('attachments.index');
        Route::get('articles', 'Article\EditorController@index')->name('articles.index');
        Route::get('options', 'Article\EditorController@options')->name('articles.options');

        // メール必須機能
        Route::middleware(['verified'])->group(function () {
            Route::post('user', 'UserController@update')->name('users.update');
            Route::post('tags', 'TagController@store')->name('tags.store');
            Route::post('attachments', 'AttachmentController@store')->name('attachments.store');
            Route::delete('attachments/{attachment}', 'AttachmentController@destroy')->name('attachments.destroy');
            Route::post('articles', 'Article\EditorController@store')->name('articles.store');
            Route::get('analytics', 'Article\AnalyticsController@index')->name('analytics.index');

            Route::middleware('can:update,article')->group(function () {
                Route::post('articles/{article}', 'Article\EditorController@update')->name('articles.update');
            });
        });
    });

    // 管理者機能
    Route::prefix('admin')->namespace('Admin')->middleware(['auth', 'admin', 'verified'])->group(function () {
        // デバッグツール
        Route::post('/flush-cache', 'Admin\DebugController@flushCache')->name('admin.debug.flushCache');
        Route::get('/error/{level}', 'Admin\DebugController@error')->name('admin.debug.error');
        Route::get('/phpinfo', 'Admin\DebugController@phpinfo')->name('admin.debug.phpinfo');

        // ユーザー管理
        Route::get('/users', 'Admin\UserController@index')->name('admin.users.index');
        Route::put('/users/{user}', 'Admin\UserController@update')->name('admin.users.update');
        Route::delete('/users/{user}', 'Admin\UserController@destroy')->name('admin.users.destroy');

        // 記事管理
        Route::get('/articles', 'Admin\ArticleController@index')->name('admin.articles.index');
        Route::put('/articles/{article}', 'Admin\ArticleController@update')->name('admin.articles.update');
        Route::delete('/articles/{article}', 'Admin\ArticleController@destroy')->name('admin.articles.destroy');
    });
});
