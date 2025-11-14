<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified'])->group(function (): void {
    Route::middleware(['restrict:update_tag'])->group(function (): void {
        Route::post('v2/tags', [\App\Http\Controllers\v2\Mypage\TagController::class, 'store']);
        Route::post('v2/tags/{tag}', [\App\Http\Controllers\v2\Mypage\TagController::class, 'update']);
    });

    Route::middleware(['restrict:update_article'])->group(function (): void {
        Route::post('v2/attachments', [\App\Http\Controllers\v2\Mypage\AttachmentController::class, 'store']);
        Route::delete('v2/attachments/{attachment}', [\App\Http\Controllers\v2\Mypage\AttachmentController::class, 'destroy']);
        Route::post('v2/articles', [\App\Http\Controllers\v2\Mypage\ArticleController::class, 'store']);
        Route::post('v2/articles/{article}', [\App\Http\Controllers\v2\Mypage\ArticleController::class, 'update']);
    });

    Route::post('v2/profile', [\App\Http\Controllers\v2\Mypage\ProfileController::class, 'update']);
    Route::post('v2/analytics', [\App\Http\Controllers\v2\Mypage\AnalyticsController::class, 'show']);
});
