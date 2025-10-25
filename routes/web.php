<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OauthController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\RedirectController;
use App\Http\Middleware\ExcludePaths;
use Illuminate\Support\Facades\Route;

Route::feeds();

// 一覧系
Route::get('/pak128-japan', [\App\Http\Controllers\v2\FrontController::class, 'pak128jp'])->name('pak.128japan');
Route::get('/pak128', [\App\Http\Controllers\v2\FrontController::class, 'pak128'])->name('pak.128');
Route::get('/pak64', [\App\Http\Controllers\v2\FrontController::class, 'pak64'])->name('pak.64');
Route::get('/pak-others', [\App\Http\Controllers\v2\FrontController::class, 'pakOthers'])->name('pak.others');
Route::get('/search', [\App\Http\Controllers\v2\FrontController::class, 'search'])->name('search');
Route::get('/announces', [\App\Http\Controllers\v2\FrontController::class, 'announces'])->name('announces');
Route::get('/pages', [\App\Http\Controllers\v2\FrontController::class, 'pages'])->name('pages');

// 特殊ページ
Route::get('/', [\App\Http\Controllers\v2\FrontController::class, 'top'])->name('index');
Route::get('/social', [\App\Http\Controllers\v2\FrontMiscController::class, 'social'])->name('social');
Route::get('/invite-simutrans-interact-meeting', [\App\Http\Controllers\v2\DiscordController::class, 'index'])->name('discord.index');
Route::post('/invite-simutrans-interact-meeting', [\App\Http\Controllers\v2\DiscordController::class, 'generate'])->name('discord.generate');

// 個別記事関連
Route::get('/users/{userIdOrNickname}/{articleSlug}', [\App\Http\Controllers\v2\FrontController::class, 'show'])->name('articles.show');
Route::get('/articles/{id}', [\App\Http\Controllers\v2\FrontController::class, 'fallbackShow'])->name('articles.fallbackShow');
Route::get('/articles/{article}/download', [\App\Http\Controllers\v2\FrontController::class, 'download'])->name('articles.download');
Route::get('/redirect/{name}', [\App\Http\Controllers\v2\FrontMiscController::class, 'redirect'])->name('redirect');

// 認証系ルート名保持用
Route::GET('mypage/reset/{token}', (new MypageController)->index(...))->name('password.reset');

// 招待
Route::middleware(['restrict:invitation_code'])->group(function (): void {
    Route::get('/invite/{invitation_code}', [\App\Http\Controllers\v2\InviteController::class, 'index'])->name('invite.index');
    Route::post('/invite/{invitation_code}', [\App\Http\Controllers\v2\InviteController::class, 'registration'])->name('invite.registration');
});

Route::get('/mypage/', (new MypageController)->index(...))->name('mypage.index');
Route::get('/mypage/{any}', (new MypageController)->index(...))->where('any', '.*');

Route::middleware(['auth:sanctum', 'admin', 'verified'])->group(function (): void {
    Route::get('/admin/', (new AdminController)->index(...))->name('admin.index');
    Route::get('/admin/oauth/twitter/authorize', [OauthController::class, 'authoroize'])->name('admin.oauth.twitter.authorize');
    Route::get('/admin/oauth/twitter/callback', [OauthController::class, 'callback'])->name('admin.oauth.twitter.callback');
    Route::get('/admin/oauth/twitter/refresh', [OauthController::class, 'refresh'])->name('admin.oauth.twitter.refresh');
    Route::get('/admin/oauth/twitter/revoke', [OauthController::class, 'revoke'])->name('admin.oauth.twitter.revoke');
});

Route::middleware([ExcludePaths::class])->group(function (): void {
    Route::fallback([RedirectController::class, 'index']);
});
