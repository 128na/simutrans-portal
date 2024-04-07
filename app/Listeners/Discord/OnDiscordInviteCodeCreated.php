<?php

declare(strict_types=1);

namespace App\Listeners\Discord;

use App\Events\Discord\DiscordInviteCodeCreated;
use Illuminate\Log\Logger;

final readonly class OnDiscordInviteCodeCreated
{
    public function __construct(private Logger $logger)
    {
    }

    public function handle(DiscordInviteCodeCreated $discordInviteCodeCreated): void
    {
        $this->logger->channel('invite')->info('Disocrd招待リンク生成', $this->getAccessInfo());
    }

    /**
     * @return array<mixed>
     */
    private function getAccessInfo(): array
    {

        return [
            'REMOTE_ADDR' => request()->server('REMOTE_ADDR', 'N/A'),
            'HTTP_REFERER' => request()->server('HTTP_REFERER', 'N/A'),
            'HTTP_USER_AGENT' => request()->server('HTTP_USER_AGENT', 'N/A'),
        ];
    }
}
