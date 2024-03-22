<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Admin\ArticleController;
use App\Http\Controllers\Api\Admin\ControllOptionController;
use App\Http\Controllers\Api\Admin\TagController as AdminTagController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Front\DiscordController;
use App\Http\Controllers\Api\Front\FrontController;
use App\Http\Controllers\Api\Front\ScreenshotController as FrontScreenshotController;
use App\Http\Controllers\Api\Mypage\AnalyticsController;
use App\Http\Controllers\Api\Mypage\AttachmentController;
use App\Http\Controllers\Api\Mypage\BulkZipController;
use App\Http\Controllers\Api\Mypage\EditorController;
use App\Http\Controllers\Api\Mypage\InvitationCodeController;
use App\Http\Controllers\Api\Mypage\LoginHistoryController;
use App\Http\Controllers\Api\Mypage\ScreenshotController;
use App\Http\Controllers\Api\Mypage\TagController;
use App\Http\Controllers\Api\Mypage\UserController;
use Illuminate\Support\Facades\Route;

// フロント
Route::prefix('front')->group(function (): void {
    // キャッシュ有効
    Route::middleware(['cache.headers:public;max_age=600;etag', 'cache.content'])->group(function (): void {
        Route::get('/ranking/', [FrontController::class, 'ranking']);
        Route::get('/pages', [FrontController::class, 'pages']);
        Route::get('/announces', [FrontController::class, 'announces']);
        Route::get('/categories/pak/{size}/none', [FrontController::class, 'categoryPakNoneAddon']);
        Route::get('/categories/pak/{size}/{slug}', [FrontController::class, 'categoryPakAddon']);
        Route::get('/categories/{type}/{slug}', [FrontController::class, 'category']);
        Route::get('/tags', [FrontController::class, 'tags']);
        Route::get('/tags/{tag}', [FrontController::class, 'tag']);
        Route::get('/users/{userIdOrNickname}', [FrontController::class, 'user']);
        Route::get('/users/{userIdOrNickname}/{articleSlug}', [FrontController::class, 'show']);
        Route::get('/search', [FrontController::class, 'search']);

        Route::get('/screenshots', (new FrontScreenshotController())->index(...));
        Route::get('/screenshots/{screenshot}', (new FrontScreenshotController())->show(...));
    });
    Route::middleware(['throttle:discordInvite'])->group(function (): void {
        Route::post('/invite-simutrans-interact-meeting', [DiscordController::class, 'index']);
    });
});

// マイページ
Route::prefix('mypage')->group(function (): void {
    Route::post('invite/{invitation_code}', [InvitationCodeController::class, 'register'])
        ->middleware(['restrict:invitation_code', 'throttle:register'])->name('invitation_code');
    // ログイン必須
    Route::middleware(['auth:sanctum'])->group(function (): void {
        Route::get('user', [UserController::class, 'index']);
        Route::get('tags', [TagController::class, 'search']);
        Route::get('attachments', [AttachmentController::class, 'index']);
        Route::get('articles', [EditorController::class, 'index']);
        Route::get('options', [EditorController::class, 'options']);
    });
    // メール認証必須
    Route::middleware(['auth:sanctum', 'verified'])->group(function (): void {
        Route::post('user', [UserController::class, 'update']);
        Route::post('tags', [TagController::class, 'store'])->middleware('restrict:update_tag');
        Route::post('tags/{tag}', [TagController::class, 'update'])->middleware('restrict:update_tag');
        Route::post('attachments', [AttachmentController::class, 'store']);
        Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy']);
        Route::post('articles', [EditorController::class, 'store'])->middleware('restrict:update_article');
        Route::middleware(['can:update,article', 'restrict:update_article'])->group(function (): void {
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
        // スクリーンショット機能
        Route::get('/screenshots', (new ScreenshotController())->index(...));
        Route::post('/screenshots', (new ScreenshotController())->store(...))->middleware('restrict:update_screenshot');
        Route::put('/screenshots/{screenshot}', (new ScreenshotController())->update(...))->middleware('restrict:update_screenshot');
        Route::delete('/screenshots/{screenshot}', (new ScreenshotController())->destroy(...))->middleware('restrict:update_screenshot');
        // ログイン履歴
        Route::get('/login_histories', (new LoginHistoryController())->index(...));
    });
});
// Admin
Route::prefix('admin')->middleware(['auth:sanctum', 'admin', 'verified'])->group(function (): void {
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
