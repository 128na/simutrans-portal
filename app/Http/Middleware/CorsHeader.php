<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsHeader
{
    /**
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request)
            ->header('Access-Control-Allow-Origin', $this->getAllowOrigin($request))
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', '*');
    }

    private function getAllowOrigin(Request $request): string
    {
        $ref = $request->server('HTTP_REFERER');
        // for debug
        if (str_starts_with($ref, 'https://pwa-dev.128-bit.net')) {
            return 'https://pwa-dev.128-bit.net';
        }

        return config('app.url');
    }
}
