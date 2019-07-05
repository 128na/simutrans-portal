<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

/**
 * 生成ページ丸ごとredisにキャッシュする
 */
class RedisCache
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

        $path = str_replace(config('app.url'), '', $request->fullUrl());

        return self::redisOrCallback($path, function() use($request, $next) {
            return $next($request);
        }, null);
    }

    /**
     * 指定キーでreidsからデータ取得を行う。
     * 値が無ければコールバックを実行し、結果をreisにセットして結果を返す
     * ビューインスタンス丸ごとはクロージャが含まれるためキャッシュできないのでcontentのみキャッシュ
     * @param string $key
     * @param closure $callback
     * @param int $expire_sec 保持期間
     * @return mixed
     */
    protected static function redisOrCallback($key, $callback, $expire_sec = 864000)
    {
        $cache = unserialize(Redis::get($key));
        if(empty($cache)) {
            $data = $callback();
            Redis::set($key, serialize($data->getContent()));
            if($expire_sec) {
                Redis::expire($key, $expire_sec);
            }
            return $data;
        }
        return response($cache);
    }
}
