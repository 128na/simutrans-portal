<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified'])->group(function (): void {
    // マイリスト API
    Route::prefix('v1/mylist')->group(function (): void {
        Route::get('/', [\App\Http\Controllers\Mypage\MyListController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Mypage\MyListController::class, 'store']);
        Route::patch('{mylist}', [\App\Http\Controllers\Mypage\MyListController::class, 'update']);
        Route::delete('{mylist}', [\App\Http\Controllers\Mypage\MyListController::class, 'destroy']);

        Route::get('{mylist}/items', [\App\Http\Controllers\Mypage\MyListController::class, 'getItems']);
        Route::post('{mylist}/items', [\App\Http\Controllers\Mypage\MyListController::class, 'storeItem']);
        Route::patch('{mylist}/items/reorder', [\App\Http\Controllers\Mypage\MyListController::class, 'reorderItems']);
        Route::patch('{mylist}/items/{item}', [\App\Http\Controllers\Mypage\MyListController::class, 'updateItem']);
        Route::delete('{mylist}/items/{item}', [\App\Http\Controllers\Mypage\MyListController::class, 'destroyItem']);
    });

    Route::middleware(['restrict:update_tag'])->group(function (): void {
        Route::post('v2/tags', [\App\Http\Controllers\Mypage\TagController::class, 'store']);
        Route::post('v2/tags/{tag}', [\App\Http\Controllers\Mypage\TagController::class, 'update']);
    });

    Route::middleware(['restrict:update_article'])->group(function (): void {
        Route::post('v2/attachments', [\App\Http\Controllers\Mypage\AttachmentController::class, 'store']);
        Route::delete('v2/attachments/{attachment}', [\App\Http\Controllers\Mypage\AttachmentController::class, 'destroy']);

        Route::post('v2/articles', [\App\Http\Controllers\Mypage\Article\CreateController::class, 'store']);
        Route::post('v2/articles/{article}', [\App\Http\Controllers\Mypage\Article\EditController::class, 'update']);
    });

    Route::post('v2/profile', [\App\Http\Controllers\Mypage\ProfileController::class, 'update']);

    Route::post('v2/analytics', [\App\Http\Controllers\Mypage\AnalyticsController::class, 'show']);
});
