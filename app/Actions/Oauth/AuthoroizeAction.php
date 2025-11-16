<?php

declare(strict_types=1);

namespace App\Actions\Oauth;

use App\Services\Twitter\PKCEService;
use Illuminate\Support\Facades\Session;

final readonly class AuthoroizeAction
{
    public function __construct(
        private PKCEService $pkceService,
    ) {}

    public function __invoke(): string
    {
        $state = $this->pkceService->generateState();
        $codeVerifier = $this->pkceService->generateCodeVerifier();
        $codeChallange = $this->pkceService->generateCodeChallenge($codeVerifier);
        Session::put('oauth2.twitter.state', $state);
        Session::put('oauth2.twitter.codeVerifier', $codeVerifier);

        return $this->pkceService->generateAuthorizeUrl($state, $codeChallange);
    }
}
