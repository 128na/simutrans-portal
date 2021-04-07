<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\Social\InvalidSocialUserException;
use App\Exceptions\Social\SocialLoginNotAllowedException;
use App\Http\Controllers\Controller;
use App\Services\Social\BaseLoginService;
use App\Services\Social\GoogleLoginService;
use App\Services\Social\TwitterLoginService;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    private TwitterLoginService $twitterLoginService;
    private GoogleLoginService $googleLoginService;

    public function __construct(
        TwitterLoginService $twitterLoginService,
        GoogleLoginService $googleLoginService
    ) {
        $this->twitterLoginService = $twitterLoginService;
        $this->googleLoginService = $googleLoginService;
    }

    public function redirect(string $driver)
    {
        // ログイン済みなら何もしない
        if (Auth::check()) {
            return redirect()->route('mypage.index');
        }

        try {
            $this->getLoginService($driver)->validateLoginRestriction();

            return Socialite::driver($driver)->redirect();

            return redirect()->route('mypage.index');
        } catch (SocialLoginNotAllowedException $e) {
            logger()->error($e->getMessage(), $e->getValues());

            return response(view('errors.feature_disabled'), 400);
        }
    }

    public function callback(string $driver)
    {
        // ログイン済みなら何もしない
        if (Auth::check()) {
            return redirect()->route('mypage.index');
        }

        try {
            $service = $this->getLoginService($driver);
            $service->validateLoginRestriction();

            $oauthUser = Socialite::driver($driver)->user();

            $user = $service->findOrRegister($oauthUser);

            Auth::login($user);

            return redirect()->route('mypage.index');
        } catch (InvalidSocialUserException $e) {
            logger()->error($e->getMessage(), $e->getValues());

            return response(view('errors.restriction'), 400);
        } catch (SocialLoginNotAllowedException $e) {
            logger()->error($e->getMessage(), $e->getValues());

            return response(view('errors.restriction'), 400);
        }
    }

    private function getLoginService(string $driver): BaseLoginService
    {
        if ($driver === 'twitter') {
            return $this->twitterLoginService;
        }
        if ($driver === 'google') {
            return $this->googleLoginService;
        }

        throw new SocialLoginNotAllowedException('不明なドライバ', $driver);
    }
}
