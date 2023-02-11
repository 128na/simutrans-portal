<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mypage\User as UserResouce;
use App\Services\Logging\AuditLogService;
use App\Services\UserService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
    public function __construct(private UserService $userService, private AuditLogService $auditLogService)
    {
        $this->redirectTo = route('mypage.index');
    }

    public function resendApi(Request $request): Response
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
        $this->resend($request);

        return response(['status' => true]);
    }

    public function notice(): RedirectResponse
    {
        return redirect()->route('verification.notice');
    }

    public function verifyApi(Request $request): UserResouce
    {
        $id = $request->route('id');
        if (! is_string($id)) {
            throw new AuthorizationException('id is not string');
        }

        $user = $request->user();
        if (is_null($user)) {
            throw new AuthorizationException('user not found');
        }
        $key = $user->getKey();
        if (! is_numeric($key)) {
            throw new AuthorizationException('key is not string');
        }
        if (! hash_equals($id, (string) $key)) {
            throw new AuthorizationException('has missmatch');
        }

        $hash = $request->route('hash');
        if (! is_string($hash)) {
            throw new AuthorizationException('hash is not string');
        }
        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException();
        }

        // 認証済み、認証OKならユーザーを返す
        if ($user->hasVerifiedEmail() || $user->markEmailAsVerified()) {
            event(new Verified($user));
            $this->auditLogService->userVerified($user);

            return new UserResouce($this->userService->getUser($user));
        }
        throw new AuthorizationException();
    }
}
