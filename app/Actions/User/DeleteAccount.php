<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Events\User\UserWithdrawn;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\Front\PublicArticleSnapshot;

class DeleteAccount
{
    public function __construct(
        private UserRepository $userRepository,
        private PublicArticleSnapshot $snapshot,
    ) {}

    public function __invoke(User $user): void
    {
        $this->snapshot->mutate(function () use ($user): void {
            $this->snapshot->invalidate();
            $user->tokens()->delete();
            $this->userRepository->destroy($user);
        });

        event(new UserWithdrawn($user));
    }
}
