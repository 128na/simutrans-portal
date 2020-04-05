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
    Route::get('articles/latest', 'ArticleController@latest')->name('latest');
    Route::get('articles/search', 'ArticleController@search')->name('search');
    Route::get('articles/user/{user}', 'ArticleController@user')->name('user');
    Route::get('articles/tag/{tag}', 'ArticleController@tag')->name('tag');
    Route::get('articles/category/{category}', 'ArticleController@category')->name('category');

    Route::middleware(['auth'])->group(function () {
        Route::get('options', 'ArticleEditorController@index');
        Route::get('tags', 'TagController@search');
        Route::post('tags', 'TagController@store');

        Route::get('attachments/{article?}', 'AttachmentController@index');
        Route::post('attachments', 'AttachmentController@store');
        Route::delete('attachments/{attachment?}', 'AttachmentController@destroy');

        Route::post('articles', 'ArticleEditorController@store');
        Route::post('articles/preview', 'ArticleEditorController@preview');
        Route::post('articles/{article}', 'ArticleEditorController@update');
    });
});
