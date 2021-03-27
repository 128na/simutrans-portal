<?php

namespace App\Http\Middleware;

use Closure;

/**
 * 改行・スペースを削除する.
 */
class MinifyHTML
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
        $res = $next($request);

        // htmlレスポンス以外はそのまま返す
        if (stripos($res->headers->get('Content-Type'), 'text/html') === false) {
            return $res;
        }

        $content = $res->getContent();
        $content = preg_replace('/(\S)\s+(\S)/', '$1 $2', $content);
        $res->setContent($content);

        return $res;
    }
}
