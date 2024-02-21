<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Listeners\BaseListener;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Log\Logger;

class OnPasswordReset extends BaseListener
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handle(PasswordReset $passwordReset): void
    {
        if ($passwordReset->user instanceof User) {
            $this->logger->channel('audit')->info('パスワードリセット', $this->getUserInfo($passwordReset->user));
        }
    }
}
