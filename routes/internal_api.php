<?php

declare(strict_types=1);

use App\Http\Controllers\Mypage\AnalyticsController;
use App\Http\Controllers\Mypage\Article\CreateController;
use App\Http\Controllers\Mypage\Article\EditController;
use App\Http\Controllers\Mypage\AttachmentController;
use App\Http\Controllers\Mypage\MyListController;
use App\Http\Controllers\Mypage\ProfileController;
use App\Http\Controllers\Mypage\TagController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified'])->group(function (): void {
    // マイリスト API
    Route::prefix('v1/mylist')->group(function (): void {
        Route::get('/', [MyListController::class, 'index']);
        Route::post('/', [MyListController::class, 'store']);
        Route::patch('{mylist}', [MyListController::class, 'update']);
        Route::delete('{mylist}', [MyListController::class, 'destroy']);

        Route::get('{mylist}/items', [MyListController::class, 'getItems']);
        Route::post('{mylist}/items', [MyListController::class, 'storeItem']);
        Route::patch('{mylist}/items/reorder', [MyListController::class, 'reorderItems']);
        Route::patch('{mylist}/items/{item}', [MyListController::class, 'updateItem']);
        Route::delete('{mylist}/items/{item}', [MyListController::class, 'destroyItem']);
    });

    Route::middleware(['restrict:update_tag'])->group(function (): void {
        Route::post('v2/tags', [TagController::class, 'store']);
        Route::post('v2/tags/{tag}', [TagController::class, 'update']);
    });

    Route::middleware(['restrict:update_article'])->group(function (): void {
        Route::post('v2/attachments', [AttachmentController::class, 'store']);
        Route::delete('v2/attachments/{attachment}', [AttachmentController::class, 'destroy']);

        Route::post('v2/articles', [CreateController::class, 'store']);
        Route::post('v2/articles/{article}', [EditController::class, 'update']);
    });

    Route::post('v2/profile', [ProfileController::class, 'update']);

    Route::post('v2/analytics', [AnalyticsController::class, 'show']);
});
