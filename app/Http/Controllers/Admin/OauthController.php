<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\OauthTokenRepository;
use App\Services\Twitter\PKCEService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OauthController extends Controller
{
    public function __construct(
        private PKCEService $pkceService,
        private OauthTokenRepository $oauthTokenRepository,
    ) {
    }

    public function authoroize(): RedirectResponse
    {
        $state = $this->pkceService->generateState();
        $codeVerifier = $this->pkceService->generateCodeVerifier();
        $codeChallange = $this->pkceService->generateCodeChallenge($codeVerifier);
        Session::put('oauth2.twitter.state', $state);
        Session::put('oauth2.twitter.codeVerifier', $codeVerifier);

        $authUrl = $this->pkceService->generateAuthorizeUrl($state, $codeChallange);

        return redirect($authUrl);
    }

    public function callback(Request $request): RedirectResponse
    {
        $this->pkceService->verifyState($request->state, Session::pull('oauth2.twitter.state'));
        $this->pkceService->generateToken($request->code, Session::pull('oauth2.twitter.codeVerifier'));

        Session::flash('success', 'access token created');

        return redirect()->route('admin.index');
    }

    public function refresh(): RedirectResponse
    {
        try {
            $token = $this->oauthTokenRepository->getToken('twitter');
            $this->pkceService->refreshToken($token);
            Session::flash('success', 'access token refreshed');
        } catch (ModelNotFoundException $e) {
            Session::flash('error', 'token not found');
        }

        return redirect()->route('admin.index');
    }

    public function revoke(): RedirectResponse
    {
        try {
            $token = $this->oauthTokenRepository->getToken('twitter');
            $this->pkceService->revokeToken($token);
            Session::flash('success', 'access token revoked');
        } catch (ModelNotFoundException $e) {
            Session::flash('error', 'token not found');
        }

        return redirect()->route('admin.index');
    }
}
