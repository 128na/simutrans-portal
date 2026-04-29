<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\ArticleStatusController;
use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Pages\PublicMyListController;
use Illuminate\Support\Facades\Route;

// 公開マイリスト閲覧（認証不要）
Route::get('v1/mylist/public', [PublicMyListController::class, 'listPublic']);
Route::get('v1/mylist/public/{slug}', [PublicMyListController::class, 'showPublic']);

// 認証済みユーザー向けAPI
Route::middleware(['auth:sanctum', 'verified'])->prefix('v1')->group(function (): void {
    Route::get('articles', [ArticleController::class, 'index']);
    Route::get('articles/{id}', [ArticleController::class, 'show']);
    Route::patch('articles/{id}/status', [ArticleStatusController::class, 'update']);
    Route::get('attachments', [AttachmentController::class, 'index']);
});
