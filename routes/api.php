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

    Route::middleware(['auth'])->group(function () {
        Route::get('attachments/my', 'AttachmentController@my');
        Route::get('attachments/myimage', 'AttachmentController@myimage');
        Route::post('attachments/upload', 'AttachmentController@upload');
        Route::delete('attachments/{attachment}', 'AttachmentController@delete');
    });
});

Route::prefix('v2')->name('api.v2.')->namespace('Api\v2')->group(function () {
    Route::get('articles/latest', 'ArticleController@latest')->name('articles.latest');
    Route::get('articles/search', 'ArticleController@search')->name('articles.search');
    Route::get('articles/user/{user}', 'ArticleController@user')->name('articles.user');
    Route::get('articles/tag/{tag}', 'ArticleController@tag')->name('articles.tag');
    Route::get('articles/category/{category}', 'ArticleController@category')->name('articles.category');

    Route::prefix('mypage')->namespace('Mypage')->middleware(['auth'])->group(function () {
        Route::get('user', 'UserController@index')->name('users.index');
        Route::get('tags', 'TagController@search')->name('tags.search');
        Route::get('attachments', 'AttachmentController@index')->name('attachments.index');
        Route::get('articles', 'ArticleController@index')->name('articles.index');
        Route::get('options', 'ArticleController@options')->name('articles.options');

        Route::middleware(['verified'])->group(function () {
            Route::post('user', 'UserController@update')->name('users.update');
            Route::post('tags', 'TagController@store')->name('tags.store');
            Route::post('attachments', 'AttachmentController@store')->name('attachments.store');
            Route::delete('attachments/{attachment}', 'AttachmentController@destroy')->name('attachments.destroy');
            Route::post('articles', 'ArticleController@store')->name('articles.store');
            Route::post('articles/{article}', 'ArticleController@update')->name('articles.update');
        });
    });
});
