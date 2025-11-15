<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * ユーザーが管理者権限を持つか確認する.
 */
final class AdminCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next): mixed
    {
        /** @var ?\App\Models\User */
        $user = Auth::user();
        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        return abort(401);
    }
}
