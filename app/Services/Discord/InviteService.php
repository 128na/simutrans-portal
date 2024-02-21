<?php

declare(strict_types=1);

namespace App\Services\Discord;

use App\Services\Service;
use Illuminate\Support\Facades\Http;

class InviteService extends Service
{
    public function __construct()
    {
    }

    public function create(): string
    {
        $response = Http::withHeaders(['Authorization' => 'Bot '.config('services.discord.token')])
            ->post(
                'https://discord.com/api/v10/channels/'.config('services.discord.channel').'/invites',
                [
                    'max_age' => (int) config('services.discord.max_age'),
                    'max_uses' => (int) config('services.discord.max_uses'),
                    'unique' => true,
                ]
            );

        $body = $response->json();

        if ($response->status() !== 200 || ! array_key_exists('code', $body)) {
            throw new CreateInviteFailedException($response->body());
        }

        return sprintf('%s/%s', config('services.discord.domain'), $body['code']);
    }
}
