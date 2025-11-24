<?php

declare(strict_types=1);

namespace App\Services\Discord;

use App\Config\EnvironmentConfig;
use Illuminate\Support\Facades\Http;

final readonly class InviteService
{
    public function __construct(
        private EnvironmentConfig $environmentConfig
    ) {}

    public function create(): string
    {
        if (! $this->environmentConfig->hasDiscord()) {
            throw new CreateInviteFailedException('Discord is not configured');
        }

        // hasDiscord() がtrueなので、これらは確実にnullではない
        assert($this->environmentConfig->discordToken !== null);
        assert($this->environmentConfig->discordChannel !== null);

        $response = Http::withHeaders(['Authorization' => 'Bot '.$this->environmentConfig->discordToken])
            ->post(
                'https://discord.com/api/v10/channels/'.$this->environmentConfig->discordChannel.'/invites',
                [
                    'max_age' => $this->environmentConfig->discordMaxAge,
                    'max_uses' => $this->environmentConfig->discordMaxUses,
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

        return sprintf('%s/%s', $this->environmentConfig->discordDomain, $body['code']);
    }
}
