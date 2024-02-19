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

    public function handle(Login $event): void
    {
        if ($event->user instanceof User) {
            $this->logger->channel('audit')->info('ログイン', $this->getUserInfo($event->user));

            $event->user->notify(new SendLoggedInEmail($event->user->loginHistories()->create()));
        }
    }
}
