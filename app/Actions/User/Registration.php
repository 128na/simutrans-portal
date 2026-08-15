<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Actions\User\Data\RegisterUserData;
use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\UserInvited;
use App\Repositories\UserRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class Registration
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function __invoke(RegisterUserData $registerUserData, User $user): User
    {
        $inviter = $this->userRepository->store([
            'name' => $registerUserData->name,
            'email' => $registerUserData->email,
            'role' => UserRole::User,
            'password' => Hash::make($registerUserData->password),
            'invited_by' => $user->id,
        ]);

        event(new Registered($inviter));
        $user->notify(new UserInvited($inviter));

        return $inviter;
    }
}
