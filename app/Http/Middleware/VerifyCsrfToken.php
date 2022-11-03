<?php

namespace App\Http\Middleware;

use App;
use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
    ];

    public function handle($request, Closure $next)
    {
        if ($this->inDevelopToken($request)) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }

    private function inDevelopToken($request): bool
    {
        return !App::environment('production') && $request->header('X-CSRF-TOKEN') === 'dummy';
    }
}
