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

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('tags', 'TagController@search')->name('tags.search');
        Route::post('tags', 'TagController@store')->name('tags.store');

        Route::get('attachments/{article?}', 'AttachmentController@index')->name('attachments.index');
        Route::post('attachments/{article?}', 'AttachmentController@store')->name('attachments.store');
        Route::delete('attachments/{attachment}/{article?}', 'AttachmentController@destroy')->name('attachments.destroy');

        Route::get('options', 'ArticleEditorController@options')->name('articles.options');
        Route::post('articles', 'ArticleEditorController@store')->name('articles.store');
        Route::post('articles/{article}', 'ArticleEditorController@update')->name('articles.update');
    });
});
