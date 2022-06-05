<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OauthToken;
use App\Services\TwitterAnalytics\PKCEService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OauthController extends Controller
{
    public function __construct(
        private PKCEService $pkceService
    ) {
    }

    public function twitterAuthoroize()
    {
        $state = $this->pkceService->generateState();
        $codeVerifier = $this->pkceService->generateCodeVerifier();
        $codeChallange = $this->pkceService->generateCodeChallenge($codeVerifier);
        Session::put('oauth2.twitter.state', $state);
        Session::put('oauth2.twitter.codeVerifier', $codeVerifier);

        $authUrl = $this->pkceService->generateAuthorizeUrl($state, $codeChallange);

        return redirect($authUrl);
    }

    public function twitterCallback(Request $request)
    {
        $state = $request->state;
        $code = $request->code;

        $this->pkceService->verifyState($state, Session::pull('oauth2.twitter.state'));

        $data = $this->pkceService->generateToken($code, Session::pull('oauth2.twitter.codeVerifier'));

        OauthToken::create($data);
    }
}
