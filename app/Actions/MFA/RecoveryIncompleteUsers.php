<?php

declare(strict_types=1);

namespace App\Actions\MFA;

use App\Events\User\TwoFactorAuthenticationIncomplete;
use App\Models\User;
use App\Notifications\MFASetupRecovered;
use App\Repositories\UserRepository;

class RecoveryIncompleteUsers
{
    public function __construct(private UserRepository $userRepository) {}

    public function __invoke(): void
    {
        $users = $this->userRepository->findIncompleteMFAUsers();
        foreach ($users as $user) {
            $this->recoverUser($user);
        }
    }

    private function recoverUser(User $user): void
    {
        $user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);
        event(new TwoFactorAuthenticationIncomplete($user));
        $user->notify(new MFASetupRecovered);
    }
}
