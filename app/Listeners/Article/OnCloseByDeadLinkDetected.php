<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\Article\CloseByDeadLinkDetected;
use App\Listeners\BaseListener;
use App\Notifications\SendDeadLinkDetectedEmail;

class OnCloseByDeadLinkDetected extends BaseListener
{
    public function handle(CloseByDeadLinkDetected $event): void
    {
        $event->article->notify(new SendDeadLinkDetectedEmail());
    }
}
