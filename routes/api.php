<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// 公開マイリスト閲覧（認証不要）
Route::get('v1/mylist/public/{slug}', [\App\Http\Controllers\Mypage\MyListController::class, 'showPublic']);
