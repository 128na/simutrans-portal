<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ユーザーが管理者権限を持つか確認する.
 */
final class AdminCheck
{
    public function handle(
        Request $request,
        Closure $next,
    ): \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse {
        /** @var ?\App\Models\User */
        $user = Auth::user();
        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        return abort(401);
    }
}
