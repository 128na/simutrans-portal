<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2;

use App\Actions\User\Registration;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRequest;
use App\Models\User;

final class InviteController extends Controller
{
    public function index(User $user): \Illuminate\Contracts\View\View
    {
        return view('v2.user.index', ['invitee' => $user]);
    }

    public function registration(User $user, StoreRequest $storeRequest, Registration $registration): \Illuminate\Contracts\View\View
    {
        $data = $storeRequest->validated();
        $inviter = $registration($data, $user);

        return view('v2.user.welcome', ['inviter' => $inviter]);
    }
}
