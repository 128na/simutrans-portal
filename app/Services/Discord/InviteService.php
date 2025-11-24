<?php

declare(strict_types=1);

namespace App\Services\Discord;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

final class InviteService
{
    public function create(): string
    {
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

        if ($response->status() !== 200 || ! is_array($body) || ! array_key_exists('code', $body)) {
            throw new CreateInviteFailedException($response->body());
        }

        /** @var array{code: mixed} $body After validation, we know 'code' key exists */
        return sprintf('%s/%s', Config::string('services.discord.domain'), $body['code']);
    }
}
