<?php

declare(strict_types=1);

namespace App\Actions\Oauth;

use App\Repositories\OauthTokenRepository;
use App\Services\Twitter\PKCEService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Session;

class RevokeAction
{
    public function __construct(
        private PKCEService $pkceService,
        private OauthTokenRepository $oauthTokenRepository,
    ) {}

    public function __invoke(): void
    {
        try {
            $token = $this->oauthTokenRepository->getToken('twitter');
            $this->pkceService->refreshToken($token);
            Session::flash('success', 'access token refreshed');
        } catch (ModelNotFoundException) {
            Session::flash('error', 'token not found');
        }
    }
}
