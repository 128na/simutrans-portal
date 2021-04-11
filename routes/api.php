<?php

use App\Http\Controllers\Api\v1\ConversionController;
use App\Http\Controllers\Api\v2\Admin\ArticleController;
use App\Http\Controllers\Api\v2\Admin\DebugController;
use App\Http\Controllers\Api\v2\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\v2\Mypage\Article\AnalyticsController;
use App\Http\Controllers\Api\v2\Mypage\Article\EditorController;
use App\Http\Controllers\Api\v2\Mypage\AttachmentController;
use App\Http\Controllers\Api\v2\Mypage\BookmarkController;
use App\Http\Controllers\Api\v2\Mypage\TagController;
use App\Http\Controllers\Api\v2\Mypage\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;

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
    Route::post('click/{article}', [ConversionController::class, 'click'])->name('click');
});

// auth
Route::prefix('v2')->name('api.v2.')->group(function () {
    // 登録
    Route::POST('register', [RegisterController::class, 'registerApi'])->name('register');
    // メール確認
    Route::POST('email/resend', [VerificationController::class, 'resendApi'])->name('verification.resend');
    // 認証
    Route::POST('login', [LoginController::class, 'login'])->name('login');
    Route::POST('logout', [LoginController::class, 'logout'])->name('logout');
    // PWリセット
    Route::POST('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
});

Route::prefix('v2')->name('api.v2.')->namespace('Api\v2')->group(function () {
    // マイページ機能
    Route::prefix('mypage')->namespace('Mypage')->middleware(['auth'])->group(function () {
        Route::get('user', [UserController::class, 'index'])->name('users.index');
        Route::get('tags', [TagController::class, 'search'])->name('tags.search');
        Route::get('attachments', [AttachmentController::class, 'index'])->name('attachments.index');
        Route::get('articles', [EditorController::class, 'index'])->name('articles.index');
        Route::get('options', [EditorController::class, 'options'])->name('articles.options');

        // メール必須機能
        Route::middleware(['verified'])->group(function () {
            Route::post('user', [UserController::class, 'update'])->name('users.update');
            Route::post('tags', [TagController::class, 'store'])->name('tags.store');
            Route::post('attachments', [AttachmentController::class, 'store'])->name('attachments.store');
            Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');
            Route::post('articles', [EditorController::class, 'store'])->name('articles.store');
            Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

            Route::middleware('can:update,article')->group(function () {
                Route::post('articles/{article}', [EditorController::class, 'update'])->name('articles.update');
            });

            Route::get('bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
            Route::post('bookmarks', [BookmarkController::class, 'store'])->name('bookmarks.store');
            Route::post('bookmarks/{bookmark}', [BookmarkController::class, 'update'])->name('bookmarks.update');
            Route::delete('bookmarks/{bookmark}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');
        });
    });

    // 管理者機能
    Route::prefix('admin')->namespace('Admin')->middleware(['auth', 'admin', 'verified'])->group(function () {
        // デバッグツール
        Route::post('/flush-cache', [DebugController::class, 'flushCache'])->name('admin.flushCache');
        Route::get('/debug/{level}', [DebugController::class, 'error'])->name('admin.debug');
        Route::get('/phpinfo', [DebugController::class, 'phpinfo'])->name('admin.phpinfo');

        // ユーザー管理
        Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

        // 記事管理
        Route::get('/articles', [ArticleController::class, 'index'])->name('admin.articles.index');
        Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('admin.articles.update');
        Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('admin.articles.destroy');
    });
});
