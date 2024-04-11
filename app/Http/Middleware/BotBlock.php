<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class BotBlock
{
    /**
     * robots.txtを守らない悪い子たち
     */
    private const array BAD_ROBOTS = [
        'claudebot',
        'petalbot',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ua = $request->server('HTTP_USER_AGENT');
        if (! is_string($ua)) {
            return response('', 200)->header('Cache-Control', 'public, max-age=2147483648');
        }

        $ua = mb_strtolower($ua);
        foreach (self::BAD_ROBOTS as $bot) {
            if (str_contains($ua, $bot)) {
                return response('', 200)->header('Cache-Control', 'public, max-age=2147483648');
            }
        }

        return $next($request);
    }
}
