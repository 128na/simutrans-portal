<?php

use Illuminate\Http\Request;

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
    Route::delete('/v1/attachments/delete/{id}', 'Api\AttachmentController@delete');
});

