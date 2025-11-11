<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OauthController;
use App\Http\Controllers\RedirectController;
use App\Http\Middleware\ExcludePaths;
use Illuminate\Support\Facades\Route;

Route::feeds();

// 一覧系
Route::get('/users', [\App\Http\Controllers\v2\Front\FrontController::class, 'users'])->name('users');
Route::get('/pak128-japan', [\App\Http\Controllers\v2\Front\FrontController::class, 'pak128jp'])->name('pak.128japan');
Route::get('/pak128', [\App\Http\Controllers\v2\Front\FrontController::class, 'pak128'])->name('pak.128');
Route::get('/pak64', [\App\Http\Controllers\v2\Front\FrontController::class, 'pak64'])->name('pak.64');
Route::get('/pak-others', [\App\Http\Controllers\v2\Front\FrontController::class, 'pakOthers'])->name('pak.others');
Route::get('/search', [\App\Http\Controllers\v2\Front\FrontController::class, 'search'])->name('search');
Route::get('/announces', [\App\Http\Controllers\v2\Front\FrontController::class, 'announces'])->name('announces');
Route::get('/pages', [\App\Http\Controllers\v2\Front\FrontController::class, 'pages'])->name('pages');

// 特殊ページ
Route::get('/', [\App\Http\Controllers\v2\Front\FrontController::class, 'top'])->name('index');
Route::get('/social', [\App\Http\Controllers\v2\Front\FrontMiscController::class, 'social'])->name('social');
Route::get('/invite-simutrans-interact-meeting', [\App\Http\Controllers\v2\Front\DiscordController::class, 'index'])->name('discord.index');
Route::post('/invite-simutrans-interact-meeting', [\App\Http\Controllers\v2\Front\DiscordController::class, 'generate'])->name('discord.generate');

// 個別記事関連
Route::get('/users/{userIdOrNickname}/{articleSlug}', [\App\Http\Controllers\v2\Front\FrontController::class, 'show'])->name('articles.show');
Route::get('/articles/{id}', [\App\Http\Controllers\v2\Front\FrontController::class, 'fallbackShow'])->name('articles.fallbackShow');
Route::get('/articles/{article}/download', [\App\Http\Controllers\v2\Front\FrontController::class, 'download'])->name('articles.download');
Route::get('/redirect/{name}', [\App\Http\Controllers\v2\Front\FrontMiscController::class, 'redirect'])->name('redirect');

// 認証・招待
Route::middleware(['restrict:invitation_code'])->group(function (): void {
    Route::get('/invite/{invitation_code}', [\App\Http\Controllers\v2\Mypage\UserController::class, 'showInvite'])->name('user.invite');
    Route::post('/invite/{invitation_code}', [\App\Http\Controllers\v2\Mypage\UserController::class, 'registration'])->name('user.registration');
});
Route::GET('/login', [\App\Http\Controllers\v2\Mypage\UserController::class, 'showLogin'])->name('login');
Route::GET('/login/2fa', [\App\Http\Controllers\v2\Mypage\UserController::class, 'showTwoFactor'])->name('two-factor.login');
Route::GET('/forgot-password', [\App\Http\Controllers\v2\Mypage\UserController::class, 'showForgotPassword'])->name('forgot-password');
Route::GET('/reset-password/{token}', [\App\Http\Controllers\v2\Mypage\UserController::class, 'showResetPassword'])->name('reset-password');

Route::middleware(['auth'])->group(function (): void {
    Route::get('/mypage/', [\App\Http\Controllers\v2\Mypage\MypageController::class, 'index'])->name('mypage.index');
    Route::get('/mypage/verify-email', [\App\Http\Controllers\v2\Mypage\MypageController::class, 'verifyEmail'])->name('mypage.verify-email');
    Route::get('/mypage/verify-required', [\App\Http\Controllers\v2\Mypage\MypageController::class, 'verifyNotice'])->name('verification.notice');

    Route::middleware(['verified'])->group(function (): void {
        Route::get('/mypage/two-factor', [\App\Http\Controllers\v2\Mypage\MypageController::class, 'twoFactor'])->name('mypage.two-factor');
        Route::get('/mypage/login-histories', [\App\Http\Controllers\v2\Mypage\MypageController::class, 'loginHistories'])->name('mypage.login-histories');

        Route::get('/mypage/redirects', [\App\Http\Controllers\v2\Mypage\RedirectController::class, 'index'])->name('mypage.redirects');
        Route::delete('/mypage/redirects/{redirect}', [\App\Http\Controllers\v2\Mypage\RedirectController::class, 'destroy'])->name('mypage.redirects.destroy');

        Route::get('/mypage/invite', [\App\Http\Controllers\v2\Mypage\InviteController::class, 'index'])->name('mypage.invite');
        Route::post('/mypage/invite', [\App\Http\Controllers\v2\Mypage\InviteController::class, 'createOrUpdate']);
        Route::delete('/mypage/invite', [\App\Http\Controllers\v2\Mypage\InviteController::class, 'revoke']);

        Route::get('/mypage/tags', [\App\Http\Controllers\v2\Mypage\TagController::class, 'index'])->name('mypage.tags');
        Route::post('/mypage/tags', [\App\Http\Controllers\v2\Mypage\TagController::class, 'store']);
        Route::post('/mypage/tags/{tag}', [\App\Http\Controllers\v2\Mypage\TagController::class, 'update']);

        Route::get('/mypage/attachments', [\App\Http\Controllers\v2\Mypage\AttachmentController::class, 'index'])->name('mypage.attachments');

        Route::get('/mypage/profile', [\App\Http\Controllers\v2\Mypage\ProfileController::class, 'index'])->name('mypage.profile');
        Route::post('/mypage/profile', [\App\Http\Controllers\v2\Mypage\ProfileController::class, 'update']);

        Route::get('/mypage/analytics', [\App\Http\Controllers\v2\Mypage\AnalyticsController::class, 'index'])->name('mypage.analytics');

        Route::get('/mypage/articles', [\App\Http\Controllers\v2\Mypage\ArticleController::class, 'index'])->name('mypage.articles.index');
        Route::get('/mypage/articles/create', [\App\Http\Controllers\v2\Mypage\ArticleController::class, 'create'])->name('mypage.articles.create');
        Route::post('/mypage/articles/create', [\App\Http\Controllers\v2\Mypage\ArticleController::class, 'store']);
        Route::get('/mypage/articles/edit/{article}', [\App\Http\Controllers\v2\Mypage\ArticleController::class, 'edit'])->name('mypage.articles.edit');
        Route::post('/mypage/articles/edit/{article}', [\App\Http\Controllers\v2\Mypage\ArticleController::class, 'update']);
    });
});

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
