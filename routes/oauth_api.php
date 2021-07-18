<?php

use App\Http\Controllers\Api\Oauth\FirebaseController;

// 公開API(OAuth認証)
Route::name('api.oauth.')->namespace('Api\Oauth')->middleware(['auth:api'])->group(function () {
    // read
    Route::middleware(['scopeAll:user-read'])->group(function () {
    });
    // write
    Route::middleware(['scopeAll:user-write'])->group(function () {
        Route::post('/firebase/{project}/login', [FirebaseController::class, 'login'])->name('firebase.login');
        Route::post('/firebase/{project}/link', [FirebaseController::class, 'link'])->name('firebase.link');
        Route::post('/firebase/{project}/unlink', [FirebaseController::class, 'unlink'])->name('firebase.unlink');
    });
});
