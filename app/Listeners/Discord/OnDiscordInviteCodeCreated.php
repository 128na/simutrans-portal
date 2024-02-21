<?php

declare(strict_types=1);

namespace App\Listeners\Discord;

use App\Events\Discord\DiscordInviteCodeCreated;
use App\Listeners\BaseListener;
use Illuminate\Log\Logger;

class OnDiscordInviteCodeCreated extends BaseListener
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handle(DiscordInviteCodeCreated $event): void
    {
        $this->logger->channel('invite')->info('Disocrd招待リンク生成', $this->getAccessInfo());
    }
}
