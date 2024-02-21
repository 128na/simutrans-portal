<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Listeners\BaseListener;
use App\Models\User;
use App\Notifications\SendLoggedInEmail;
use Illuminate\Auth\Events\Login;
use Illuminate\Log\Logger;

class OnLogin extends BaseListener
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handle(Login $login): void
    {
        if ($login->user instanceof User) {
            $this->logger->channel('audit')->info('ログイン', $this->getUserInfo($login->user));

            $login->user->notify(new SendLoggedInEmail($login->user->loginHistories()->create()));
        }
    }
}
