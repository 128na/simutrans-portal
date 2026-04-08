<?php

declare(strict_types=1);

use App\Http\Controllers\Pages\PublicMyListController;
use Illuminate\Support\Facades\Route;

// 公開マイリスト閲覧（認証不要）
Route::get('v1/mylist/public', [PublicMyListController::class, 'listPublic']);
Route::get('v1/mylist/public/{slug}', [PublicMyListController::class, 'showPublic']);
