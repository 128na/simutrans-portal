<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\Social\InvalidSocialUserException;
use App\Exceptions\Social\SocialLoginNotAllowedException;
use App\Http\Controllers\Controller;
use App\Services\TwitterLoginService;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class TwitterLoginController extends Controller
{
    private TwitterLoginService $twitterLoginService;

    public function __construct(
        TwitterLoginService $twitterLoginService
    ) {
        $this->twitterLoginService = $twitterLoginService;
    }

    public function redirect()
    {
        // ログイン済みなら何もしない
        if (Auth::check()) {
            return redirect()->route('mypage.index');
        }

        return Socialite::driver('twitter')->redirect();
    }

    public function callback()
    {
        // ログイン済みなら何もしない
        if (Auth::check()) {
            return redirect()->route('mypage.index');
        }

        try {
            $twitterUser = Socialite::driver('twitter')->user();

            $user = $this->twitterLoginService->findOrRegister($twitterUser);

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
}
