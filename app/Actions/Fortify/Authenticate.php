<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Notifications\Loggedin;
use App\Services\Logging\AuditLogService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class Authenticate
{
    public static function auth(): Closure
    {
        return function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if (
                $user &&
                Hash::check($request->password, $user->password)
            ) {
                $loginHistory = $user->loginHistories()->create();
                $user->notify(new Loggedin($loginHistory));
                app(AuditLogService::class)->userLoggedIn($user);

                return $user;
            }
        };
    }
}
