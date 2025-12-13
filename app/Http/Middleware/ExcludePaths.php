<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ExcludePaths
{
    /**
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $excludedPaths = [
            '*.php',
            '*.js',
        ];

        foreach ($excludedPaths as $excludedPath) {
            if (fnmatch($excludedPath, $request->path())) {
                return response('', 404);
            }
        }

        return $next($request);
    }
}
