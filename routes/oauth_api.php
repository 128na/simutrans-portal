<?php

use App\Http\Controllers\Api\Oauth\UserController;

// 公開API(OAuth認証)
Route::name('api.oauth.')->namespace('Api\Oauth')->middleware(['auth:api'])->group(function () {
    // read
    Route::middleware(['scopeAll:user-read'])->group(function () {
        Route::get('/user', [UserController::class, 'show'])->name('user.show');
    });
    // write
    Route::middleware(['scopeAll:user-write'])->group(function () {

    });

});
