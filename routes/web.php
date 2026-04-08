<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\OauthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Mypage\AnalyticsController;
use App\Http\Controllers\Mypage\Article\CreateController;
use App\Http\Controllers\Mypage\Article\EditController;
use App\Http\Controllers\Mypage\AttachmentController;
use App\Http\Controllers\Mypage\DashboardController;
use App\Http\Controllers\Mypage\InviteController;
use App\Http\Controllers\Mypage\MyListPageController;
use App\Http\Controllers\Mypage\ProfileController;
use App\Http\Controllers\Pages\Article\DownloadController;
use App\Http\Controllers\Pages\Article\IndexController;
use App\Http\Controllers\Pages\Article\PakController;
use App\Http\Controllers\Pages\Article\ShowController;
use App\Http\Controllers\Pages\CategoryController;
use App\Http\Controllers\Pages\DiscordController;
use App\Http\Controllers\Pages\PublicMyListController;
use App\Http\Controllers\Pages\SocialController;
use App\Http\Controllers\Pages\TagController;
use App\Http\Controllers\Pages\TopController;
use App\Http\Controllers\Pages\UserController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\ExcludePaths;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

// POSTなど表示デバッグしづらいルート用
if (App::environment('local')) {
    Route::get('/playground', fn (): Factory|View => view('mypage.playground'));
    Route::get('/invite-welcome', fn (): Factory|View => view('auth.welcome', [
        'inviter' => new User(['name' => 'dummy']),
    ]));
    Route::get('/two-factor', fn (): Factory|View => view('auth.two-factor'));
}

Route::feeds();

// 一覧系
Route::get('/latest', [PakController::class, 'latest'])->name('latest');
Route::get('/pak128-japan', [PakController::class, 'pak128jp'])->name('pak.128japan');
Route::get('/pak128', [PakController::class, 'pak128'])->name('pak.128');
Route::get('/pak64', [PakController::class, 'pak64'])->name('pak.64');
Route::get('/pak-others', [PakController::class, 'pakOthers'])->name('pak.others');
Route::get('/search', [IndexController::class, 'search'])->name('search');
Route::get('/announces', [IndexController::class, 'announces'])->name('announces');
Route::get('/pages', [IndexController::class, 'pages'])->name('pages');

Route::get('/tags', [TagController::class, 'tags'])->name('tags.index');
Route::get('/tags/{tag}', [TagController::class, 'tag'])->name('tags.show');

Route::get('/categories', [CategoryController::class, 'categories'])->name('categories.index');
Route::get('/categories/pak/{pak}/{addon}', [CategoryController::class, 'categoryPakAddon'])->name('categories.pakAddon');

// 特殊ページ
Route::get('/', [TopController::class, 'top'])->name('index');
Route::get('/social', [SocialController::class, 'social'])->name('social');
Route::get('/invite-simutrans-interact-meeting', [DiscordController::class, 'index'])->name('discord.index');
Route::post('/invite-simutrans-interact-meeting', [DiscordController::class, 'generate'])->name('discord.generate');

// ユーザー別
Route::get('/users', [UserController::class, 'users'])->name('users.index');
Route::get('/users/{userIdOrNickname}', [UserController::class, 'user'])->name('users.show');
Route::get('/users/{userIdOrNickname}/{articleSlug}', [ShowController::class, 'show'])->name('articles.show')->where('articleSlug', '.*');

// 公開マイリスト
Route::get('/mylist', [PublicMyListController::class, 'index'])->name('public-mylist.index');
Route::get('/mylist/{slug}', [PublicMyListController::class, 'show'])->name('public-mylist.show');

// 記事詳細・ダウンロード
Route::get('/articles/{id}', [ShowController::class, 'fallbackShow'])->name('articles.fallbackShow');
Route::get('/articles/{article}/download', [DownloadController::class, 'download'])->name('articles.download');
Route::get('/articles/{article}/conversion', [DownloadController::class, 'conversion'])->name('articles.conversion');
Route::get('/redirect/{name}', [RedirectController::class, 'redirect'])->name('redirect');

// 認証・招待
Route::middleware(['restrict:invitation_code'])->group(function (): void {
    Route::get('/invite/{invitation_code}', [RegisterController::class, 'showInvite'])->name('user.invite');
    Route::post('/invite/{invitation_code}', [RegisterController::class, 'registration'])->name('user.registration');
});

Route::GET('/login', [LoginController::class, 'showLogin'])->name('login');
Route::GET('/login/2fa', [TwoFactorController::class, 'showTwoFactor'])->name('two-factor.login');
Route::GET('/forgot-password', [PasswordController::class, 'showForgotPassword'])->name('forgot-password');
Route::GET('/reset-password/{token}', [PasswordController::class, 'showResetPassword'])->name('reset-password');

Route::middleware(['auth'])->group(function (): void {
    Route::get('/mypage/', [DashboardController::class, 'index'])->name('mypage.index');
    Route::get('/mypage/verify-email', [DashboardController::class, 'verifyEmail'])->name('mypage.verify-email');
    Route::get('/mypage/verify-required', [DashboardController::class, 'verifyNotice'])->name('verification.notice');

    Route::middleware(['verified'])->group(function (): void {
        Route::get('/mypage/two-factor', [DashboardController::class, 'twoFactor'])->name('mypage.two-factor');
        Route::get('/mypage/login-histories', [DashboardController::class, 'loginHistories'])->name('mypage.login-histories');

        Route::get('/mypage/redirects', [\App\Http\Controllers\Mypage\RedirectController::class, 'index'])->name('mypage.redirects');
        Route::delete('/mypage/redirects/{redirect}', [\App\Http\Controllers\Mypage\RedirectController::class, 'destroy'])->name('mypage.redirects.destroy');

        Route::get('/mypage/invite', [InviteController::class, 'index'])->name('mypage.invite');
        Route::post('/mypage/invite', [InviteController::class, 'createOrUpdate']);
        Route::delete('/mypage/invite', [InviteController::class, 'revoke']);

        Route::get('/mypage/tags', [\App\Http\Controllers\Mypage\TagController::class, 'index'])->name('mypage.tags');
        Route::post('/mypage/tags', [\App\Http\Controllers\Mypage\TagController::class, 'store']);
        Route::post('/mypage/tags/{tag}', [\App\Http\Controllers\Mypage\TagController::class, 'update']);

        Route::get('/mypage/attachments', [AttachmentController::class, 'index'])->name('mypage.attachments');

        Route::get('/mypage/profile', [ProfileController::class, 'index'])->name('mypage.profile');
        Route::post('/mypage/profile', [ProfileController::class, 'update']);

        Route::get('/mypage/analytics', [AnalyticsController::class, 'index'])->name('mypage.analytics');

        Route::get('/mypage/mylists', [MyListPageController::class, 'index'])->name('mypage.mylists.index');
        Route::get('/mypage/mylists/{mylist}', [MyListPageController::class, 'show'])->name('mypage.mylists.show');

        Route::get('/mypage/articles', [\App\Http\Controllers\Mypage\Article\IndexController::class, 'index'])->name('mypage.articles.index');
        Route::get('/mypage/articles/create', [CreateController::class, 'create'])->name('mypage.articles.create');
        Route::post('/mypage/articles/create', [CreateController::class, 'store']);
        Route::get('/mypage/articles/edit/{article}', [EditController::class, 'edit'])->name('mypage.articles.edit');
        Route::post('/mypage/articles/edit/{article}', [EditController::class, 'update']);
    });
});

Route::middleware(['auth:sanctum', 'admin', 'verified'])->group(function (): void {
    Route::get('/admin', [OauthController::class, 'index'])->name('admin.index');
    Route::get('/admin/oauth/twitter/authorize', [OauthController::class, 'authoroize'])->name('admin.oauth.twitter.authorize');
    Route::get('/admin/oauth/twitter/callback', [OauthController::class, 'callback'])->name('admin.oauth.twitter.callback');
    Route::get('/admin/oauth/twitter/refresh', [OauthController::class, 'refresh'])->name('admin.oauth.twitter.refresh');
    Route::get('/admin/oauth/twitter/revoke', [OauthController::class, 'revoke'])->name('admin.oauth.twitter.revoke');
});

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::middleware([ExcludePaths::class])->group(function (): void {
    Route::fallback([RedirectController::class, 'index']);
});
