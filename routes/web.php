<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['cache.content'])->group(function () {
    Route::feeds();
});

// 認証系ルート名保持用
Route::GET('mypage/verify/{id}/{hash}', [MypageController::class, 'index'])->name('verification.verify');
Route::get('/verification/notice', [MypageController::class, 'index'])->name('verification.notice');
Route::GET('mypage/reset/{token}', [MypageController::class, 'index'])->name('password.reset');
Route::POST('login', [LoginController::class, 'login'])->middleware('restrict:login')->name('login');

// フロント
// マイページ
// Admin

// 招待
Route::GET('/mypage/invite/{invitation_code}', [InviteController::class, 'index'])->middleware('restrict:invitation_code')->name('invite.index');

// 非ログイン系 reidsキャッシュ有効
Route::middleware(['cache.headers:public;max_age=2628000;etag', 'cache.content'])->group(function () {
    Route::get('/', [FrontController::class, 'fallback'])->name('index');
    Route::get('/ranking', [FrontController::class, 'fallback'])->name('ranking');
    Route::get('/pages', [FrontController::class, 'fallback'])->name('pages');
    Route::get('/announces', [FrontController::class, 'fallback'])->name('announces');
    Route::get('/category/pak/{size}/none', [FrontController::class, 'categoryPakNoneAddon'])->name('category.pak.noneAddon');
    Route::get('/category/pak/{size}/{slug}', [FrontController::class, 'categoryPakAddon'])->name('category.pak.addon');
    Route::get('/category/{type}/{slug}', [FrontController::class, 'category'])->name('category');
    Route::get('/tag/{tag}', [FrontController::class, 'tag'])->name('tag');
    Route::get('/user/{user}', [FrontController::class, 'user'])->name('user');
    Route::get('/tags', [FrontController::class, 'fallback'])->name('tags');
    Route::get('/invite-simutrans-interact-meeting', [FrontController::class, 'fallback']);
    Route::get('/social', [FrontController::class, 'social']);
});
// 非ログイン系 reidsキャッシュ無効
Route::get('/articles/{article}', [FrontController::class, 'show'])->name('articles.show');
Route::get('/search', [FrontController::class, 'search'])->name('search');
Route::get('/mypage/', [MypageController::class, 'index'])->name('mypage.index');
Route::get('/mypage/{any}', [MypageController::class, 'index'])->where('any', '.*');
Route::get('/articles/{article}/download', [FrontController::class, 'download'])->name('articles.download');

Route::middleware(['auth:sanctum', 'admin', 'verified'])->group(function () {
    Route::get('/admin/', [AdminController::class, 'index'])->name('admin.index');
});

Route::get('/error/{status}', [FrontController::class, 'error'])->name('error');

Route::fallback([RedirectController::class, 'index']);
