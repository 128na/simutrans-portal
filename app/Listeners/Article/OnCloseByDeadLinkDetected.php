<?php

declare(strict_types=1);

namespace App\Listeners\Article;

use App\Events\Article\CloseByDeadLinkDetected;
use App\Listeners\BaseListener;
use App\Notifications\SendDeadLinkDetectedEmail;

class OnCloseByDeadLinkDetected extends BaseListener
{
    public function handle(CloseByDeadLinkDetected $closeByDeadLinkDetected): void
    {
        $closeByDeadLinkDetected->article->notify(new SendDeadLinkDetectedEmail());
    }
}
