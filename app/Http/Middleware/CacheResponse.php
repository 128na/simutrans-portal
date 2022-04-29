<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * 生成ページ丸ごとキャッシュする.
 */
class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // ログインしているユーザーはキャッシュを使用しない
        if (Auth::check() || app()->environment('local')) {
            return $next($request);
        }

        $key = str_replace(config('app.url'), '', $request->fullUrl());
        // $locale = \App::getLocale();
        // $key = "{$path}@{$locale}";

        return self::cacheOrCallback($key, fn () => $next($request));
    }

    /**
     * 指定キーでキャッシュからデータ取得を行う。
     * 値が無ければコールバックを実行し、結果をキャッシュにセットして結果を返す
     * ビューインスタンス丸ごとはクロージャが含まれるためキャッシュできないのでcontentのみキャッシュ.
     *
     * @param string  $key
     * @param closure $callback
     *
     * @return mixed
     */
    protected static function cacheOrCallback($key, $callback)
    {
        try {
            $cache = Cache::get($key);
        } catch (Exception $e) {
            report($e);

            return $callback();
        }

        if (empty($cache)) {
            $data = $callback();

            // エラーレスポンスはキャッシュしない
            if ($data->getStatusCode() !== 200 || !$data->getContent()) {
                return $data;
            }
            $cache = gzencode($data->getContent());

            // 空データ、キャッシュ失敗？
            if (strlen($cache) < 100) {
                return $data;
            }
            Cache::put($key, $cache, config('app.cache_lifetime_min', 0) * 60);
        }

        return response($cache)->header('Content-Encoding', 'gzip');
    }
}
