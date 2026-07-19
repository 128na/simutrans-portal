<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | CORS_ALLOWED_ORIGINS may be unset, empty, or contain a comma-separated
    | list of origins; both an unset and an empty value fall back to APP_URL.
    | Because `supports_credentials` below is always true (this app relies on
    | Sanctum's SPA cookie authentication), a wildcard origin would allow any
    | site to make credentialed requests, so it is explicitly rejected here
    | rather than being silently accepted by the browser as "no CORS".
    |
    */
    'allowed_origins' => (static function (): array {
        $origins = trim((string) env('CORS_ALLOWED_ORIGINS', ''));

        if ($origins === '') {
            $origins = (string) env('APP_URL', 'http://localhost');
        }

        $allowedOrigins = array_values(array_filter(array_map('trim', explode(',', $origins))));

        if (in_array('*', $allowedOrigins, true)) {
            throw new RuntimeException(
                'CORS_ALLOWED_ORIGINS must not contain "*" because supports_credentials is enabled; specify explicit origins instead.'
            );
        }

        return $allowedOrigins;
    })(),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
