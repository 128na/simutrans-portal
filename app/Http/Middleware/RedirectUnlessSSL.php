<?php

namespace App\Http\Middleware;

use Closure;

class RedirectUnlessSSL
{
    /**
     * 本番、ステージング環境でhttpアクセスの場合リダイレクトする
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->secure() && \App::environment('production', 'staging')) {
            return redirect()->secure($request->fullUrl());
        }

        return $next($request);
    }
}
