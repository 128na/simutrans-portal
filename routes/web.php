<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OauthController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\RedirectController;
use App\Http\Middleware\ExcludePaths;
use Illuminate\Support\Facades\Route;

Route::middleware(['cache.content'])->group(function (): void {
    Route::feeds();
});

// 認証系ルート名保持用
Route::GET('mypage/reset/{token}', (new MypageController)->index(...))->name('password.reset');

// 招待
Route::GET('/mypage/invite/{invitation_code}', (new InviteController)->index(...))->middleware('restrict:invitation_code')->name('invite.index');

// 非ログイン系 reidsキャッシュ有効
Route::middleware(['cache.headers:public;max_age=2628000;etag', 'cache.content'])->group(function (): void {
    Route::get('/', [FrontController::class, 'fallback'])->name('index');
    Route::get('/ranking', [FrontController::class, 'fallback'])->name('ranking');
    Route::get('/pages', [FrontController::class, 'fallback'])->name('pages');
    Route::get('/announces', [FrontController::class, 'fallback'])->name('announces');
    Route::get('/categories/pak/{size}/none', [FrontController::class, 'categoryPakNoneAddon'])->name('category.pak.noneAddon');
    Route::get('/categories/pak/{size}/{slug}', [FrontController::class, 'categoryPakAddon'])->name('category.pak.addon');
    Route::get('/categories/{type}/{slug}', [FrontController::class, 'category'])->name('category');
    Route::get('/tags', [FrontController::class, 'fallback'])->name('tags');
    Route::get('/tags/{tag}', [FrontController::class, 'tag'])->name('tag');
    Route::get('/users/{userIdOrNickname}', [FrontController::class, 'user'])->name('user');
    Route::get('/invite-simutrans-interact-meeting', [FrontController::class, 'fallback']);
    Route::get('/social', [FrontController::class, 'social']);
});
// 非ログイン系 reidsキャッシュ無効
Route::get('/articles/{id}', [FrontController::class, 'fallbackShow']);
Route::get('/search', [FrontController::class, 'search'])->name('search');
Route::get('/mypage/', (new MypageController)->index(...))->name('mypage.index');
Route::get('/mypage/{any}', (new MypageController)->index(...))->where('any', '.*');
Route::get('/users/{userIdOrNickname}/{articleSlug}', [FrontController::class, 'show'])->name('articles.show');
Route::post('/articles/{article}/download', [FrontController::class, 'download'])->name('articles.download');
Route::middleware(['throttle:external'])->group(function (): void {
    Route::get('/articles/{article}/download', [FrontController::class, 'downloadFromExternal']);
});

Route::middleware(['auth:sanctum', 'admin', 'verified'])->group(function (): void {
    Route::get('/admin/', (new AdminController)->index(...))->name('admin.index');
    Route::get('/admin/oauth/twitter/authorize', [OauthController::class, 'authoroize'])->name('admin.oauth.twitter.authorize');
    Route::get('/admin/oauth/twitter/callback', [OauthController::class, 'callback'])->name('admin.oauth.twitter.callback');
    Route::get('/admin/oauth/twitter/refresh', [OauthController::class, 'refresh'])->name('admin.oauth.twitter.refresh');
    Route::get('/admin/oauth/twitter/revoke', [OauthController::class, 'revoke'])->name('admin.oauth.twitter.revoke');
});

Route::get('/error/{status}', [FrontController::class, 'error'])->name('error');

Route::middleware([ExcludePaths::class])->group(function (): void {
    Route::fallback([RedirectController::class, 'index']);
});
