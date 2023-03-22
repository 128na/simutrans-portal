<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class Cacheable
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        if ($this->shouldCache()) {
            $key = $this->getKey($request);

            /** @var string|false $cached */
            $cached = Cache::get($key, false);
            if ($cached) {
                return response($cached, 200, ['X-Cache-Key' => $key, 'Content-Encoding' => 'gzip']);
            }

            $content = $response->getContent();
            if ($content) {
                Cache::put($key, gzencode($content, 9), config('app.cache_lifetime_min') * 60);
            }
        }

        return $response;
    }

    private function shouldCache(): bool
    {
        return (bool) App::environment(['production']);
    }

    private function getKey(Request $request): string
    {
        return sha1($request->fullUrl());
    }
}
