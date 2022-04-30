<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Front\ArticleController;
use App\Http\Controllers\Front\IndexController;
use App\Http\Controllers\Front\PublicBookmarkController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\User\AdvancedSearchController;
use App\Http\Controllers\User\BookmarkItemController;

Route::feeds();

// メール確認
Route::middleware(['auth'])->group(function () {
    Route::GET('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
});
Route::get('/verification/notice', [VerificationController::class, 'notice'])->name('verification.notice');
// PWリセット
Route::POST('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
Route::GET('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');

// 非ログイン系 reidsキャッシュ有効
Route::middleware(['cache.response'])->group(function () {
    Route::get('/', [IndexController::class, 'index'])->name('index');
    Route::get('/addons', [ArticleController::class, 'addons'])->name('addons.index');
    Route::get('/ranking', [ArticleController::class, 'ranking'])->name('addons.ranking');
    Route::get('/pages', [ArticleController::class, 'pages'])->name('pages.index');
    Route::get('/announces', [ArticleController::class, 'announces'])->name('announces.index');
    Route::get('/category/pak/{size}/none', [ArticleController::class, 'categoryPakNoneAddon'])->name('category.pak.noneAddon');
    Route::get('/category/pak/{size}/{slug}', [ArticleController::class, 'categoryPakAddon'])->name('category.pak.addon');
    Route::get('/category/{type}/{slug}', [ArticleController::class, 'category'])->name('category');
    Route::get('/tag/{tag}', [ArticleController::class, 'tag'])->name('tag');
    Route::get('/user/{user}', [ArticleController::class, 'user'])->name('user');
    Route::get('/tags', [ArticleController::class, 'tags'])->name('tags');

    Route::get('/public-bookmarks', [PublicBookmarkController::class, 'index'])->name('publicBookmarks.index');
    Route::get('/public-bookmarks/{uuid}', [PublicBookmarkController::class, 'show'])->name('publicBookmarks.show');
});
// 非ログイン系 reidsキャッシュ無効
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/search', [ArticleController::class, 'search'])->name('search');
Route::get('/mypage/', [MypageController::class, 'index'])->name('mypage.index');
Route::get('/mypage/{any}', [MypageController::class, 'fallback'])->where('any', '.*');
Route::get('/articles/{article}/download', [ArticleController::class, 'download'])->name('articles.download');

// ログイン系 reidsキャッシュ無効
Route::middleware(['verified'])->group(function () {
    Route::match(['get', 'post'], '/advancedSearch', [AdvancedSearchController::class, 'search'])->name('advancedSearch');
    Route::post('/bookmark-items', [BookmarkItemController::class, 'store'])->name('bookmarkItems.store');
});

Route::middleware(['auth', 'admin', 'verified'])->group(function () {
    Route::get('/admin/', [AdminController::class, 'index'])->name('admin.index');
});

Route::get('/invite/{invitation_code}', [InviteController::class, 'index'])->name('invite.index');
Route::post('/invite/{invitation_code}', [InviteController::class, 'store'])->name('invite.store');

Route::fallback([RedirectController::class, 'index']);
