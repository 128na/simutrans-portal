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
Route::post('/v1/click/{article}', 'Api\ConversionController@click');

Route::middleware(['auth'])->group(function () {

    Route::get('/v1/attachments/my', 'Api\AttachmentController@my');
    Route::get('/v1/attachments/myimage', 'Api\AttachmentController@myimage');
    Route::post('/v1/attachments/upload', 'Api\AttachmentController@upload');
    Route::delete('/v1/attachments/{attachment}', 'Api\AttachmentController@delete');
});

Route::get('/v1/articles', 'Api\ArticleController@index');

Route::prefix('v2')->name('api.v2.')->group(function () {
    Route::get('articles/latest', 'Api\v2\ArticleController@latest')->name('latest');
    Route::get('articles/search', 'Api\v2\ArticleController@search')->name('search');
    Route::get('articles/user/{user}', 'Api\v2\ArticleController@user')->name('user');
    Route::get('articles/tag/{tag}', 'Api\v2\ArticleController@tag')->name('tag');
    Route::get('articles/category/{category}', 'Api\v2\ArticleController@category')->name('category');
});
