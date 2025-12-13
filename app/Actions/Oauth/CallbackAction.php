<?php

declare(strict_types=1);

namespace App\Actions\Oauth;

use App\Services\Twitter\Exceptions\InvalidStateException;
use App\Services\Twitter\PKCEService;
use Illuminate\Support\Facades\Session;

class CallbackAction
{
    public function __construct(
        private PKCEService $pkceService,
    ) {}

    public function __invoke(string $state, string $code): void
    {
        $sessionState = Session::pull('oauth2.twitter.state');
        if (! is_string($sessionState)) {
            throw new InvalidStateException('state is not string');
        }

        $this->pkceService->verifyState($state, $sessionState);
        $codeVerifier = Session::pull('oauth2.twitter.codeVerifier');
        if (! is_string($codeVerifier)) {
            throw new InvalidStateException('codeVerifier is not string');
        }

        $this->pkceService->generateToken($code, $codeVerifier);

        Session::flash('success', 'access token created');
    }
}
