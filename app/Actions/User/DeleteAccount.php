<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Events\User\UserWithdrawn;
use App\Models\User;
use App\Repositories\UserRepository;

class DeleteAccount
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function __invoke(User $user): void
    {
        $this->userRepository->destroy($user);

        event(new UserWithdrawn($user));
    }
}
