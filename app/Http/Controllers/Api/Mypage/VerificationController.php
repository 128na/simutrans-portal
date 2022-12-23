<?php

namespace App\Http\Controllers\Api\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mypage\User as UserResouce;
use App\Services\UserService;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
     */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(private UserService $userService)
    {
        $this->redirectTo = route('mypage.index');
    }

    public function resendApi(Request $request)
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
        $this->resend($request);

        return response(['status' => true]);
    }

    public function notice()
    {
        return response(view('errors.verification'), 401);
    }

    public function verifyApi(Request $request)
    {
        if (! hash_equals((string) $request->route('id'), (string) $request->user()->getKey())) {
            throw new AuthorizationException();
        }

        if (! hash_equals((string) $request->route('hash'), sha1($request->user()->getEmailForVerification()))) {
            throw new AuthorizationException();
        }

        // 認証済み、認証OKならユーザーを返す
        if ($request->user()->hasVerifiedEmail() || $request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));

            $user = $this->userService->getUser(Auth::user());

            return new UserResouce($user);
        }
        throw new AuthorizationException();
    }
}
