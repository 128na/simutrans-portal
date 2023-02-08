<?php

declare(strict_types=1);

namespace App\Services\Discord;

use App\Services\Service;
use RestCord\DiscordClient;

class InviteService extends Service
{
    public function __construct(
        private DiscordClient $discordClient,
    ) {
    }

    public function create(): string
    {
        $result = $this->discordClient->channel->createChannelInvite([
            'channel.id' => (int) config('services.discord.channel'),
            'max_age' => (int) config('services.discord.max_age'),
            'max_uses' => (int) config('services.discord.max_uses'),
            'temporary' => true,
            'unique' => true,
        ]);

        return sprintf('%s/%s', config('services.discord.domain'), $result->code);
    }
}
