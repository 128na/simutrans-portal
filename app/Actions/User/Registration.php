<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\UserInvited;
use App\Repositories\v2\UserRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

final readonly class Registration
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    /**
     * @param array{
     *     name: string,
     *     email: string,
     *     password: string,
     * } $data
     */
    public function __invoke(array $data, User $user): User
    {
        $inviter = $this->userRepository->store([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => UserRole::User,
            'password' => Hash::make($data['password']),
            'invited_by' => $user->id,
        ]);

        event(new Registered($inviter));
        $user->notify(new UserInvited($inviter));

        return $inviter;
    }
}
