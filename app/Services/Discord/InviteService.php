<?php

declare(strict_types=1);

namespace App\Services\Discord;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class InviteService
{
    public function create(): string
    {
        /** @var \Illuminate\Http\Client\Response */
        $response = Http::withHeaders(['Authorization' => 'Bot '.Config::string('services.discord.token')])
            ->post(
                'https://discord.com/api/v10/channels/'.Config::string('services.discord.channel').'/invites',
                [
                    'max_age' => Config::integer('services.discord.max_age'),
                    'max_uses' => Config::integer('services.discord.max_uses'),
                    'unique' => true,
                ]
            );

        /**
         * @var array<string, mixed>
         */
        $body = $response->json();

        if ($response->status() !== 200 || ! array_key_exists('code', $body)) {
            throw new CreateInviteFailedException($response->body());
        }

        $code = is_string($body['code']) ? $body['code'] : '';

        return sprintf('%s/%s', Config::string('services.discord.domain'), $code);
    }
}
