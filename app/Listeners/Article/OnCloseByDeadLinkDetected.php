<?php

declare(strict_types=1);

namespace App\Listeners\Article;

use App\Events\Article\CloseByDeadLinkDetected;
use App\Notifications\SendDeadLinkDetectedEmail;

final class OnCloseByDeadLinkDetected
{
    public function handle(CloseByDeadLinkDetected $closeByDeadLinkDetected): void
    {
        $closeByDeadLinkDetected->article->notify(new SendDeadLinkDetectedEmail);
    }
}
