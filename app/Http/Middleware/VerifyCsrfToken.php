<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Auth\HostSessionGuard;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [];

    #[\Override]
    protected function addCookieToResponse($request, $response): Response
    {
        $response = parent::addCookieToResponse($request, $response);
        $domain = config('session.legacy_domain');

        if (! is_string($domain) || $domain === '') {
            return $response;
        }

        $domain = '.'.ltrim($domain, '.');
        $host = $request->getHost();
        if ($host !== ltrim($domain, '.') && ! str_ends_with($host, $domain)) {
            return $response;
        }

        $names = ['XSRF-TOKEN', config('session.legacy_cookie')];
        $guard = Auth::guard('web');
        if ($guard instanceof HostSessionGuard) {
            $names[] = $guard->getLegacyRecallerName();
        }

        foreach ($names as $name) {
            $response->headers->clearCookie($name, '/', $domain, config('session.secure'), $name !== 'XSRF-TOKEN', 'lax');
        }

        return $response;
    }
}
