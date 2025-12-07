<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\OauthController;
use App\Http\Controllers\RedirectController;
use App\Http\Middleware\ExcludePaths;
use Illuminate\Support\Facades\Route;

Route::feeds();

// 一覧系
Route::get('/pak128-japan', [\App\Http\Controllers\Pages\Article\PakController::class, 'pak128jp'])->name('pak.128japan');
Route::get('/pak128', [\App\Http\Controllers\Pages\Article\PakController::class, 'pak128'])->name('pak.128');
Route::get('/pak64', [\App\Http\Controllers\Pages\Article\PakController::class, 'pak64'])->name('pak.64');
Route::get('/pak-others', [\App\Http\Controllers\Pages\Article\PakController::class, 'pakOthers'])->name('pak.others');
Route::get('/search', [\App\Http\Controllers\Pages\Article\IndexController::class, 'search'])->name('search');
Route::get('/announces', [\App\Http\Controllers\Pages\Article\IndexController::class, 'announces'])->name('announces');
Route::get('/pages', [\App\Http\Controllers\Pages\Article\IndexController::class, 'pages'])->name('pages');

Route::get('/tags', [\App\Http\Controllers\Pages\TagController::class, 'tags'])->name('tags.index');
Route::get('/tags/{tag}', [\App\Http\Controllers\Pages\TagController::class, 'tag'])->name('tags.show');

Route::get('/categories', [\App\Http\Controllers\Pages\CategoryController::class, 'categories'])->name('categories.index');
Route::get('/categories/pak/{pak}/{addon}', [\App\Http\Controllers\Pages\CategoryController::class, 'categoryPakAddon'])->name('categories.pakAddon');

// 特殊ページ
Route::get('/', [\App\Http\Controllers\Pages\TopController::class, 'top'])->name('index');
Route::get('/social', [\App\Http\Controllers\Pages\SocialController::class, 'social'])->name('social');
Route::get('/invite-simutrans-interact-meeting', [\App\Http\Controllers\Pages\DiscordController::class, 'index'])->name('discord.index');
Route::post('/invite-simutrans-interact-meeting', [\App\Http\Controllers\Pages\DiscordController::class, 'generate'])->name('discord.generate');

// ユーザー別
Route::get('/users', [\App\Http\Controllers\Pages\UserController::class, 'users'])->name('users.index');
Route::get('/users/{userIdOrNickname}', [\App\Http\Controllers\Pages\UserController::class, 'user'])->name('users.show');
Route::get('/users/{userIdOrNickname}/{articleSlug}', [\App\Http\Controllers\Pages\Article\ShowController::class, 'show'])->name('articles.show')->where('articleSlug', '.*');
// 記事詳細・ダウンロード
Route::get('/articles/{id}', [\App\Http\Controllers\Pages\Article\ShowController::class, 'fallbackShow'])->name('articles.fallbackShow');
Route::get('/articles/{article}/download', [\App\Http\Controllers\Pages\Article\DownloadController::class, 'download'])->name('articles.download');
Route::get('/articles/{article}/conversion', [\App\Http\Controllers\Pages\Article\DownloadController::class, 'conversion'])->name('articles.conversion');
Route::get('/redirect/{name}', [RedirectController::class, 'redirect'])->name('redirect');

// 認証・招待
Route::middleware(['restrict:invitation_code'])->group(function (): void {
    Route::get('/invite/{invitation_code}', [\App\Http\Controllers\Auth\RegisterController::class, 'showInvite'])->name('user.invite');
    Route::post('/invite/{invitation_code}', [\App\Http\Controllers\Auth\RegisterController::class, 'registration'])->name('user.registration');
});

Route::GET('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLogin'])->name('login');
Route::GET('/login/2fa', [\App\Http\Controllers\Auth\TwoFactorController::class, 'showTwoFactor'])->name('two-factor.login');
Route::GET('/forgot-password', [\App\Http\Controllers\Auth\PasswordController::class, 'showForgotPassword'])->name('forgot-password');
Route::GET('/reset-password/{token}', [\App\Http\Controllers\Auth\PasswordController::class, 'showResetPassword'])->name('reset-password');

Route::middleware(['auth'])->group(function (): void {
    Route::get('/mypage/', [\App\Http\Controllers\Mypage\DashboardController::class, 'index'])->name('mypage.index');
    Route::get('/mypage/verify-email', [\App\Http\Controllers\Mypage\DashboardController::class, 'verifyEmail'])->name('mypage.verify-email');
    Route::get('/mypage/verify-required', [\App\Http\Controllers\Mypage\DashboardController::class, 'verifyNotice'])->name('verification.notice');

    Route::middleware(['verified'])->group(function (): void {
        Route::get('/mypage/two-factor', [\App\Http\Controllers\Mypage\DashboardController::class, 'twoFactor'])->name('mypage.two-factor');
        Route::get('/mypage/login-histories', [\App\Http\Controllers\Mypage\DashboardController::class, 'loginHistories'])->name('mypage.login-histories');

        Route::get('/mypage/redirects', [\App\Http\Controllers\Mypage\RedirectController::class, 'index'])->name('mypage.redirects');
        Route::delete('/mypage/redirects/{redirect}', [\App\Http\Controllers\Mypage\RedirectController::class, 'destroy'])->name('mypage.redirects.destroy');

        Route::get('/mypage/invite', [\App\Http\Controllers\Mypage\InviteController::class, 'index'])->name('mypage.invite');
        Route::post('/mypage/invite', [\App\Http\Controllers\Mypage\InviteController::class, 'createOrUpdate']);
        Route::delete('/mypage/invite', [\App\Http\Controllers\Mypage\InviteController::class, 'revoke']);

        Route::get('/mypage/tags', [\App\Http\Controllers\Mypage\TagController::class, 'index'])->name('mypage.tags');
        Route::post('/mypage/tags', [\App\Http\Controllers\Mypage\TagController::class, 'store']);
        Route::post('/mypage/tags/{tag}', [\App\Http\Controllers\Mypage\TagController::class, 'update']);

        Route::get('/mypage/attachments', [\App\Http\Controllers\Mypage\AttachmentController::class, 'index'])->name('mypage.attachments');

        Route::get('/mypage/profile', [\App\Http\Controllers\Mypage\ProfileController::class, 'index'])->name('mypage.profile');
        Route::post('/mypage/profile', [\App\Http\Controllers\Mypage\ProfileController::class, 'update']);

        Route::get('/mypage/analytics', [\App\Http\Controllers\Mypage\AnalyticsController::class, 'index'])->name('mypage.analytics');

        Route::get('/mypage/articles', [\App\Http\Controllers\Mypage\Article\IndexController::class, 'index'])->name('mypage.articles.index');
        Route::get('/mypage/articles/create', [\App\Http\Controllers\Mypage\Article\CreateController::class, 'create'])->name('mypage.articles.create');
        Route::post('/mypage/articles/create', [\App\Http\Controllers\Mypage\Article\CreateController::class, 'store']);
        Route::get('/mypage/articles/edit/{article}', [\App\Http\Controllers\Mypage\Article\EditController::class, 'edit'])->name('mypage.articles.edit');
        Route::post('/mypage/articles/edit/{article}', [\App\Http\Controllers\Mypage\Article\EditController::class, 'update']);
    });

    Route::get('/mypage/playground', fn (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View => view('mypage.playground'));
});

Route::middleware(['auth:sanctum', 'admin', 'verified'])->group(function (): void {
    Route::get('/admin', [OauthController::class, 'index'])->name('admin.index');
    Route::get('/admin/oauth/twitter/authorize', [OauthController::class, 'authoroize'])->name('admin.oauth.twitter.authorize');
    Route::get('/admin/oauth/twitter/callback', [OauthController::class, 'callback'])->name('admin.oauth.twitter.callback');
    Route::get('/admin/oauth/twitter/refresh', [OauthController::class, 'refresh'])->name('admin.oauth.twitter.refresh');
    Route::get('/admin/oauth/twitter/revoke', [OauthController::class, 'revoke'])->name('admin.oauth.twitter.revoke');
});

Route::middleware([ExcludePaths::class])->group(function (): void {
    Route::fallback([RedirectController::class, 'index']);
});
