<?php

declare(strict_types=1);

namespace Abordage\LaravelHtmlMin\Middleware;

use Abordage\LaravelHtmlMin\Facades\HtmlMin;
use BeyondCode\ServerTiming\Facades\ServerTiming;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HtmlMinify
{
    /**
     * @return Response|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (! $this->compressionPossible($request, $response)) {
            return $response;
        }

        $html = $response->getContent();

        if (class_exists('\BeyondCode\ServerTiming\Facades\ServerTiming')) {
            ServerTiming::start('Minification');
        }

        $htmlMin = HtmlMin::minify($html);

        if (class_exists('\BeyondCode\ServerTiming\Facades\ServerTiming')) {
            ServerTiming::stop('Minification');
        }

        return $response->setContent($htmlMin);
    }

    /**
     * @param  mixed  $response
     */
    private function compressionPossible(Request $request, $response): bool
    {
        if (! config('html-min.enable')) {
            return false;
        }

        if (! in_array(strtoupper($request->getMethod()), ['GET', 'HEAD'])) {
            return false;
        }

        if (! $response instanceof Response) {
            return false;
        }

        if ($response->getStatusCode() >= 500) {
            return false;
        }

        return true;
    }
}
