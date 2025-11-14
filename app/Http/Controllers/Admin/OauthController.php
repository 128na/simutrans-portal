<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\OauthTokenRepository;
use App\Services\Twitter\Exceptions\InvalidStateException;
use App\Services\Twitter\PKCEService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

final class OauthController extends Controller
{
    public function __construct(
        private readonly PKCEService $pkceService,
        private readonly OauthTokenRepository $oauthTokenRepository,
    ) {}

    public function index(): \Illuminate\Contracts\View\View
    {
        return view('v2.admin.index');
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
        $state = Session::pull('oauth2.twitter.state');
        if (! is_string($state)) {
            throw new InvalidStateException('state is not string');
        }

        $this->pkceService->verifyState((string) $request->string('state'), $state);
        $codeVerifier = Session::pull('oauth2.twitter.codeVerifier');
        if (! is_string($codeVerifier)) {
            throw new InvalidStateException('codeVerifier is not string');
        }

        $this->pkceService->generateToken((string) $request->string('code'), $codeVerifier);

        Session::flash('success', 'access token created');

        return to_route('admin.index');
    }

    public function refresh(): RedirectResponse
    {
        try {
            $token = $this->oauthTokenRepository->getToken('twitter');
            $this->pkceService->refreshToken($token);
            Session::flash('success', 'access token refreshed');
        } catch (ModelNotFoundException) {
            Session::flash('error', 'token not found');
        }

        return to_route('admin.index');
    }

    public function revoke(): RedirectResponse
    {
        try {
            $token = $this->oauthTokenRepository->getToken('twitter');
            $this->pkceService->revokeToken($token);
            Session::flash('success', 'access token revoked');
        } catch (ModelNotFoundException) {
            Session::flash('error', 'token not found');
        }

        return to_route('admin.index');
    }
}
