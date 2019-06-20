<?php

namespace App\Http\Middleware;

use Closure;

/**
 * 改行・スペースを削除する
 */
class MinifyHTML
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
        $res = $next($request);
        $content = $res->getContent();
        $content = preg_replace('/(>)\s+(<)/', '$1$2', $content);
        $res->setContent($content);
        return $res;
    }
}
