<?php

declare(strict_types=1);

namespace App\Services\Discord;

use App\Config\EnvironmentConfig;
use Illuminate\Support\Facades\Http;

final class InviteService
{
    public function __construct(
        private readonly EnvironmentConfig $config
    ) {}

    public function create(): string
    {
        if (! $this->config->hasDiscord()) {
            throw new CreateInviteFailedException('Discord is not configured');
        }

        // hasDiscord() がtrueなので、これらは確実にnullではない
        assert($this->config->discordToken !== null);
        assert($this->config->discordChannel !== null);

        $response = Http::withHeaders(['Authorization' => 'Bot '.$this->config->discordToken])
            ->post(
                'https://discord.com/api/v10/channels/'.$this->config->discordChannel.'/invites',
                [
                    'max_age' => $this->config->discordMaxAge,
                    'max_uses' => $this->config->discordMaxUses,
                    'unique' => true,
                ]
            );

        /**
         * @var array{code:int}
         */
        $body = $response->json();

        if ($response->status() !== 200 || ! array_key_exists('code', $body)) {
            throw new CreateInviteFailedException($response->body());
        }

        return sprintf('%s/%s', $this->config->discordDomain, $body['code']);
    }
}
