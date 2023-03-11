<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Admin\ArticleController;
use App\Http\Controllers\Api\Admin\ControllOptionController;
use App\Http\Controllers\Api\Admin\TagController as AdminTagController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Front\DiscordController;
use App\Http\Controllers\Api\Front\FrontController;
use App\Http\Controllers\Api\Mypage\AnalyticsController;
use App\Http\Controllers\Api\Mypage\AttachmentController;
use App\Http\Controllers\Api\Mypage\BulkZipController;
use App\Http\Controllers\Api\Mypage\EditorController;
use App\Http\Controllers\Api\Mypage\ForgotPasswordController;
use App\Http\Controllers\Api\Mypage\InvitationCodeController;
use App\Http\Controllers\Api\Mypage\ResetPasswordController;
use App\Http\Controllers\Api\Mypage\TagController;
use App\Http\Controllers\Api\Mypage\UserController;
use App\Http\Controllers\Api\Mypage\VerificationController;
use App\Http\Controllers\LoginController;

// 認証
Route::POST('/email/resend', [VerificationController::class, 'resendApi']);
Route::GET('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyApi']);
Route::POST('/email/reset', [ResetPasswordController::class, 'reset']);
Route::POST('/logout', [LoginController::class, 'logout']);
Route::POST('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);

// フロント
Route::prefix('front')->group(function () {
    // キャッシュ有効
    Route::middleware(['cache.headers:public;max_age=600;etag'])->group(function () {
        Route::get('/top', [FrontController::class, 'top']);
        Route::get('/sidebar', [FrontController::class, 'sidebar']);
        Route::get('/', [FrontController::class, 'index']);
        Route::get('/ranking/', [FrontController::class, 'ranking']);
        Route::get('/pages', [FrontController::class, 'pages']);
        Route::get('/announces', [FrontController::class, 'announces']);
        Route::get('/category/pak/{size}/none', [FrontController::class, 'categoryPakNoneAddon']);
        Route::get('/category/pak/{size}/{slug}', [FrontController::class, 'categoryPakAddon']);
        Route::get('/category/{type}/{slug}', [FrontController::class, 'category']);
        Route::get('/tag/{tag}', [FrontController::class, 'tag']);
        Route::get('/user/{user}', [FrontController::class, 'user']);
        Route::get('/tags', [FrontController::class, 'tags']);
        Route::get('/search', [FrontController::class, 'search']);
        Route::get('/articles/{article}', [FrontController::class, 'show']);
    });

    Route::middleware(['throttle:discordInvite'])->group(function () {
        Route::post('/invite-simutrans-interact-meeting', [DiscordController::class, 'index']);
    });
});

// マイページ
Route::prefix('mypage')->group(function () {
    Route::post('invite/{invitation_code}', [InvitationCodeController::class, 'register'])
        ->middleware(['restrict:invitation_code', 'throttle:register']);
    // ログイン必須
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('user', [UserController::class, 'index']);
        Route::get('tags', [TagController::class, 'search']);
        Route::get('attachments', [AttachmentController::class, 'index']);
        Route::get('articles', [EditorController::class, 'index']);
        Route::get('options', [EditorController::class, 'options']);
    });
    // メール認証必須
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::post('user', [UserController::class, 'update']);
        Route::post('tags', [TagController::class, 'store'])->middleware('restrict:update_tag');
        Route::post('tags/{tag}', [TagController::class, 'update'])->middleware('restrict:update_tag');
        Route::post('attachments', [AttachmentController::class, 'store']);
        Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy']);
        Route::post('articles', [EditorController::class, 'store'])->middleware('restrict:update_article');
        Route::middleware(['can:update,article', 'restrict:update_article'])->group(function () {
            Route::post('articles/{article}', [EditorController::class, 'update']);
        });
        // 記事分析
        Route::get('analytics', [AnalyticsController::class, 'index']);
        // 一括DL機能
        Route::get('/bulk-zip', [BulkZipController::class, 'user']);

        // 招待機能
        Route::get('/invitation_code', [InvitationCodeController::class, 'index']);
        Route::post('/invitation_code', [InvitationCodeController::class, 'update']);
        Route::delete('/invitation_code', [InvitationCodeController::class, 'destroy']);
    });
});
// Admin
Route::prefix('admin')->middleware(['auth:sanctum', 'admin', 'verified'])->group(function () {
    // ユーザー管理
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);

    // 記事管理
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::put('/articles/{article}', [ArticleController::class, 'update']);
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy']);
    Route::post('/tags/{tag}/toggleEditable', [AdminTagController::class, 'toggleEditable']);
    Route::get('/controll_options', [ControllOptionController::class, 'index']);
    Route::post('/controll_options/{controllOption}/toggle', [ControllOptionController::class, 'toggle']);
});
