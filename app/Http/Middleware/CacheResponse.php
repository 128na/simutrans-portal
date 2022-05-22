<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * 生成ページ丸ごとキャッシュする.
 */
class CacheResponse extends SetCacheHeaders
{
    public function handle($request, Closure $next, $options = [])
    {
        // ログインしているユーザーはキャッシュを使用しない
        if (Auth::check() || app()->environment('local')) {
            return $next($request);
        }

        $key = str_replace(config('app.url'), '', $request->fullUrl());
        // $locale = \App::getLocale();
        // $key = "{$path}@{$locale}";

        $lifetime = config('app.cache_lifetime_min', 0) * 60;
        $data = self::cacheOrCallback($key, fn () => $next($request), $lifetime);

        if (is_string($options)) {
            $options = $this->parseOptions($options);
        }

        if (isset($options['etag']) && $options['etag'] === true) {
            $options['etag'] = md5($data);
        }

        if (isset($options['last_modified'])) {
            if (is_numeric($options['last_modified'])) {
                $options['last_modified'] = Carbon::createFromTimestamp($options['last_modified']);
            } else {
                $options['last_modified'] = Carbon::parse($options['last_modified']);
            }
        }
        $response = response($data);
        $response->header('Content-Encoding', 'gzip');
        $response->setCache($options);
        $response->isNotModified($request);

        return $response;
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
    protected static function cacheOrCallback($key, $callback, $lifetime)
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
            Cache::put($key, $cache, $lifetime);
        }

        return $cache;
    }
}
