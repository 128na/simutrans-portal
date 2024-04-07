<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Log\Logger;

final readonly class OnPasswordReset
{
    public function __construct(private Logger $logger)
    {
    }

    public function handle(PasswordReset $passwordReset): void
    {
        if ($passwordReset->user instanceof User) {
            $this->logger->channel('audit')->info('パスワードリセット', $passwordReset->user->getInfoLogging());
        }
    }
}
