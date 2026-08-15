<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Actions\User\DeleteAccount;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\WithdrawRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function destroy(WithdrawRequest $withdrawRequest, DeleteAccount $deleteAccount): JsonResponse
    {
        $user = $this->loggedinUser();

        $deleteAccount($user);

        Auth::guard('web')->logout();

        if ($withdrawRequest->hasSession()) {
            $withdrawRequest->session()->invalidate();
            $withdrawRequest->session()->regenerateToken();
        }

        return response()->json();
    }
}
