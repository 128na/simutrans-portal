<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * 生成ページ丸ごとキャッシュする
 */
class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // ログインしているユーザーはキャッシュを使用しない
        if (Auth::check()) {
            return $next($request);
        }

        $path = str_replace($request->url(), '', $request->fullUrl());
        $locale = \App::getLocale();
        $key = "{$path}@{$locale}";

        return self::cacheOrCallback($key, function () use ($request, $next) {
            return $next($request);
        });
    }

    /**
     * 指定キーでキャッシュからデータ取得を行う。
     * 値が無ければコールバックを実行し、結果をキャッシュにセットして結果を返す
     * ビューインスタンス丸ごとはクロージャが含まれるためキャッシュできないのでcontentのみキャッシュ
     * @param string $key
     * @param closure $callback
     * @param int $expire_sec
     * @return mixed
     */
    protected static function cacheOrCallback($key, $callback)
    {
        $cache = Cache::get($key);
        if (empty($cache)) {
            $data = $callback();

            // エラーレスポンスはキャッシュしない
            if ($data->getStatusCode() !== 200) {
                return $data;
            }

            Cache::put($key, $data->getContent(), config('app.cache_lifetime_min', 0) * 60);
            return $data;
        }
        return response($cache);
    }
}
