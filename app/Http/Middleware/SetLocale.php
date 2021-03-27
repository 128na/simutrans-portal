<?php

namespace App\Http\Middleware;

use Closure;

class SetLocale
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
        $lang = $request->cookie('lang', config('app.fallback_locale'));

        if (array_key_exists($lang, config('languages'))) {
            \App::setLocale($lang);
        }

        return $next($request);
    }
}
