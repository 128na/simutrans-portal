<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Api\Mypage\User as UserResouce;
use App\Models\User;
use App\Notifications\Loggedin;
use App\Services\Logging\AuditLogService;
use App\Services\UserService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

/**
 * ログインのみセッション認証が必要なのでwebアクセスにする.
 */
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(private UserService $userService, private AuditLogService $auditLogService)
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated($request, $user)
    {
        /** @var User */
        $user = Auth::user() ?? $user;
        $loginHistory = $user->loginHistories()->create();
        $user->notify(new Loggedin($loginHistory));
        $this->auditLogService->userLoggedIn($user);

        $user = $this->userService->getUser($user);

        return new UserResouce($user);
    }
}
