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
    Route::get('articles', 'ArticleController@index');
});

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
});
