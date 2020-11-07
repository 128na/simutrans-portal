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
Route::feeds();

// メール確認
Route::GET('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::get('/verification/notice', 'Auth\VerificationController@notice')->name('verification.notice');
// PWリセット
Route::POST('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
Route::GET('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');

// 非ログイン系 reidsキャッシュ有効
Route::middleware(['cache.response'])->group(function () {
    Route::get('/', 'Front\IndexController@index')->name('index');
    Route::get('/addons', 'Front\ArticleController@addons')->name('addons.index');
    Route::get('/ranking', 'Front\ArticleController@ranking')->name('addons.ranking');
    Route::get('/pages', 'Front\ArticleController@pages')->name('pages.index');
    Route::get('/announces', 'Front\ArticleController@announces')->name('announces.index');
    Route::get('/category/pak/{size}/{slug}', 'Front\ArticleController@categoryPakAddon')->name('category.pak.addon');
    Route::get('/category/{type}/{slug}', 'Front\ArticleController@category')->name('category');
    Route::get('/tag/{tag}', 'Front\ArticleController@tag')->name('tag');
    Route::get('/user/{user}', 'Front\ArticleController@user')->name('user');
    Route::get('/tags', 'Front\ArticleController@tags')->name('tags');
});
// 非ログイン系 reidsキャッシュ無効
Route::get('/articles/{article}', 'Front\ArticleController@show')->name('articles.show');
Route::get('/search', 'Front\ArticleController@search')->name('search');
Route::get('/mypage/', 'MypageController@index')->name('mypage.index');
Route::get('/mypage/{any}', 'RedirectController@mypage')->where('any', '.*');
Route::get('/articles/{article}/download', 'Front\ArticleController@download')->name('articles.download');

Route::middleware(['auth', 'admin', 'verified'])->group(function () {
    Route::get('/admin/', 'AdminController@index')->name('admin.index');
});

Route::fallback('RedirectController@index');
