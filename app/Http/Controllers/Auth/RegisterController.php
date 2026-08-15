<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\User\Data\RegisterUserData;
use App\Actions\User\Registration;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRequest;
use App\Models\User;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;

class RegisterController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function showInvite(User $user): View
    {
        return view('auth.invite', [
            'invitee' => $user,
            'meta' => $this->metaOgpService->mypageRegistration(),
        ]);
    }

    public function registration(User $user, StoreRequest $storeRequest, Registration $registration): View
    {
        /** @var array{name:string,email:string,password:string} $validated */
        $validated = $storeRequest->validated();
        $registerUserData = RegisterUserData::fromArray($validated);
        $inviter = $registration($registerUserData, $user);

        return view('auth.welcome', [
            'inviter' => $inviter,
            'meta' => $this->metaOgpService->mypageLogin(),
        ]);
    }
}
