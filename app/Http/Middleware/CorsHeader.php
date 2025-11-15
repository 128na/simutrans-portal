<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

final class CorsHeader
{
    public function handle(
        Request $request,
        Closure $next,
    ): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response {
        return $next($request)
            ->header('Access-Control-Allow-Origin', $this->getAllowOrigin())
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', '*');
    }

    private function getAllowOrigin(): string
    {
        return Config::string('app.url');
    }
}
