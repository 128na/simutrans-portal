<?php

use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\VerificationController;
use App\Http\Controllers\Api\v2\Admin\ArticleController;
use App\Http\Controllers\Api\v2\Admin\ControllOptionController;
use App\Http\Controllers\Api\v2\Admin\TagController as AdminTagController;
use App\Http\Controllers\Api\v2\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\v2\Mypage\Article\AnalyticsController;
use App\Http\Controllers\Api\v2\Mypage\Article\EditorController;
use App\Http\Controllers\Api\v2\Mypage\AttachmentController;
use App\Http\Controllers\Api\v2\Mypage\TagController;
use App\Http\Controllers\Api\v2\Mypage\UserController;
use App\Http\Controllers\Api\v3\BulkZipController;
use App\Http\Controllers\Api\v3\ConversionController;
use App\Http\Controllers\Api\v3\FrontController;
use App\Http\Controllers\Api\v3\InvitationCodeController;
use App\Http\Controllers\LoginController;
use App\Http\Middleware\VerifyCsrfToken;

// auth
// メール確認
Route::POST('/email/resend', [VerificationController::class, 'resendApi']);
Route::GET('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyApi']);
Route::POST('/email/reset', [ResetPasswordController::class, 'reset']);
Route::POST('/logout', [LoginController::class, 'logout']);
Route::POST('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);

Route::prefix('v2')->name('api.v2.')->group(function () {
    // マイページ機能
    Route::prefix('mypage')->middleware(['auth:sanctum'])->group(function () {
        Route::get('user', [UserController::class, 'index'])->name('users.index');
        Route::get('tags', [TagController::class, 'search'])->name('tags.search');
        Route::get('attachments', [AttachmentController::class, 'index'])->name('attachments.index');
        Route::get('articles', [EditorController::class, 'index'])->name('articles.index');
        Route::get('options', [EditorController::class, 'options'])->name('articles.options');

        // メール必須機能
        Route::middleware(['verified'])->group(function () {
            Route::post('user', [UserController::class, 'update'])->name('users.update');
            Route::post('tags', [TagController::class, 'store'])->middleware('restrict:update_tag')->name('tags.store');
            Route::post('tags/{tag}', [TagController::class, 'update'])->middleware('restrict:update_tag')->name('tags.update');
            Route::post('attachments', [AttachmentController::class, 'store'])->name('attachments.store');
            Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');
            Route::post('articles', [EditorController::class, 'store'])->middleware('restrict:update_article')->name('articles.store');
            Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

            Route::middleware(['can:update,article', 'restrict:update_article'])->group(function () {
                Route::post('articles/{article}', [EditorController::class, 'update'])->name('articles.update');
            });
        });
    });

    // 管理者機能
    Route::prefix('admin')->middleware(['auth:sanctum', 'admin', 'verified'])->group(function () {
        // ユーザー管理
        Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

        // 記事管理
        Route::get('/articles', [ArticleController::class, 'index'])->name('admin.articles.index');
        Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('admin.articles.update');
        Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('admin.articles.destroy');
        Route::post('/tags/{tag}/toggleEditable', [AdminTagController::class, 'toggleEditable'])->name('tags.toggleEditable');
        Route::get('/controll_options', [ControllOptionController::class, 'index'])->name('controllOptions.index');
        Route::post('/controll_options/{controllOption}/toggle', [ControllOptionController::class, 'toggle'])->name('controllOptions.toggle');
    });
});

Route::prefix('v3')->name('api.v3.')->group(function () {
    Route::withoutMiddleware([VerifyCsrfToken::class])->group(function () {
        Route::post('conversion/{article}', [ConversionController::class, 'conversion'])->name('conversion');
        Route::post('shown/{article}', [ConversionController::class, 'shown'])->name('shown');
    });

    Route::prefix('mypage')->middleware(['auth:sanctum', 'verified'])->group(function () {
        // 一括DL機能
        Route::get('/bulk-zip', [BulkZipController::class, 'user'])->name('bulkZip.user');

        // 招待機能
        Route::get('/invitation_code', [InvitationCodeController::class, 'index'])->name('invitationCode.index');
        Route::post('/invitation_code', [InvitationCodeController::class, 'update'])->name('invitationCode.update');
        Route::delete('/invitation_code', [InvitationCodeController::class, 'destroy'])->name('invitationCode.destroy');
    });
    Route::post('/mypage/invite/{invitation_code}', [InvitationCodeController::class, 'register'])->middleware('restrict:invitation_code')->name('invitationCode.register');

    Route::prefix('front')->group(function () {
        // キャッシュ有効
        Route::middleware(['cache.headers:public;max_age=600;etag'])->group(function () {
            Route::get('/sidebar', [FrontController::class, 'sidebar'])->name('sidebar');
            Route::get('/', [FrontController::class, 'index'])->name('index');
            Route::get('/ranking/', [FrontController::class, 'ranking'])->name('addons.ranking');
            Route::get('/pages', [FrontController::class, 'pages'])->name('pages.index');
            Route::get('/announces', [FrontController::class, 'announces'])->name('announces.index');
            Route::get('/category/pak/{size}/none', [FrontController::class, 'categoryPakNoneAddon'])->name('category.pak.noneAddon');
            Route::get('/category/pak/{size}/{slug}', [FrontController::class, 'categoryPakAddon'])->name('category.pak.addon');
            Route::get('/category/{type}/{slug}', [FrontController::class, 'category'])->name('category');
            Route::get('/tag/{tag}', [FrontController::class, 'tag'])->name('tag');
            Route::get('/user/{user}', [FrontController::class, 'user'])->name('user');
            Route::get('/tags', [FrontController::class, 'tags'])->name('tags');
            Route::get('/search', [FrontController::class, 'search'])->name('search');
            Route::get('/articles/{article}', [FrontController::class, 'show'])->name('articles.show');
        });
    });
});
