<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Admin\ArticleController;
use App\Http\Controllers\Api\Admin\ControllOptionController;
use App\Http\Controllers\Api\Admin\TagController as AdminTagController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Mypage\AnalyticsController;
use App\Http\Controllers\Api\Mypage\AttachmentController;
use App\Http\Controllers\Api\Mypage\BulkZipController;
use App\Http\Controllers\Api\Mypage\EditorController;
use App\Http\Controllers\Api\Mypage\InvitationCodeController;
use App\Http\Controllers\Api\Mypage\LoginHistoryController;
use App\Http\Controllers\Api\Mypage\RedirectController;
use App\Http\Controllers\Api\Mypage\TagController;
use App\Http\Controllers\Api\Mypage\UserController;
use Illuminate\Support\Facades\Route;

// マイページ
Route::prefix('mypage')->group(function (): void {
    Route::post('invite/{invitation_code}', [InvitationCodeController::class, 'register'])
        ->middleware(['restrict:invitation_code', 'throttle:register'])->name('invitation_code');
    // ログイン必須
    Route::middleware(['auth:sanctum'])->group(function (): void {
        Route::get('user', [UserController::class, 'index']);
        Route::get('tags', [TagController::class, 'search']);
        Route::get('attachments', [AttachmentController::class, 'index']);
        Route::get('articles', (new EditorController)->index(...));
        Route::get('options', (new EditorController)->options(...));
        Route::get('/invitation_code', [InvitationCodeController::class, 'index']);
        // ログイン履歴
        Route::get('/login_histories', (new LoginHistoryController)->index(...));
        // リダイレクト
        Route::get('/redirects', [RedirectController::class, 'index']);
        Route::delete('/redirects/{redirect}', [RedirectController::class, 'destroy']);
    });
    // メール認証必須
    Route::middleware(['auth:sanctum', 'verified'])->group(function (): void {
        Route::post('user', [UserController::class, 'update']);
        Route::post('tags', [TagController::class, 'store'])->middleware('restrict:update_tag');
        Route::post('tags/{tag}', [TagController::class, 'update'])->middleware('restrict:update_tag');
        Route::post('attachments', [AttachmentController::class, 'store']);
        Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy']);
        Route::post('articles', (new EditorController)->store(...))->middleware('restrict:update_article');
        Route::middleware(['can:update,article', 'restrict:update_article'])->group(function (): void {
            Route::post('articles/{article}', (new EditorController)->update(...));
        });
        // 記事分析
        Route::get('analytics', (new AnalyticsController)->index(...));
        // 一括DL機能
        Route::get('/bulk-zip', [BulkZipController::class, 'user']);
        // 招待機能
        Route::post('/invitation_code', [InvitationCodeController::class, 'update']);
        Route::delete('/invitation_code', [InvitationCodeController::class, 'destroy']);
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
