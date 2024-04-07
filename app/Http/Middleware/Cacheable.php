<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class Cacheable
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|Response
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        if ($this->shouldCache()) {
            $key = $this->getKey($request);
            $cached = $this->getCache($key);
            if ($cached !== null && $cached !== '' && $cached !== '0') {
                return $this->responseFromCache($cached, $key);
            }

            $this->putCache($response, $key);
        }

        return $response;
    }

    private function shouldCache(): bool
    {
        return (bool) App::environment(['production']);
    }

    private function getKey(Request $request): string
    {
        return sprintf('%s-%s', Config::string('app.version'), sha1($request->fullUrl()));
    }

    private function getCache(string $key): ?string
    {
        if (Cache::has($key)) {
            $data = Cache::get($key);
            if (is_string($data)) {
                return $data;
            }
        }

        return null;
    }

    private function responseFromCache(string $cached, string $key): Response
    {
        return response($cached, 200, ['X-Cache-Key' => $key, 'Content-Encoding' => 'gzip']);
    }

    private function putCache(Response $response, string $key): void
    {
        $content = $response->getContent();
        if ($content) {
            Cache::put($key, gzencode($content, 9), (int) Config::string('app.cache_lifetime_min') * 60);
        }
    }
}
