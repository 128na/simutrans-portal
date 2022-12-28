<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class RedirectUnlessSSL
{
    /**
     * 本番、ステージング環境でhttpアクセスの場合リダイレクトする.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $request->secure() && App::environment('production', 'staging')) {
            return redirect()->secure($request->path());
        }
        if (App::environment('ngrok')) {
            URL::forceScheme('https');
        }

        return $next($request);
    }
}
