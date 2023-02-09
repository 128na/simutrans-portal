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
        $result = Http::withHeaders(['Authorization' => 'Bot '.config('services.discord.token')])
            ->post(
                'https://discord.com/api/v10/channels/'.config('services.discord.channel').'/invites',
                [
                    'max_age' => (int) config('services.discord.max_age'),
                    'max_uses' => (int) config('services.discord.max_uses'),
                    'unique' => true,
                ]
            );

        $body = $result->json();

        if ($result->status() !== 200 || ! array_key_exists('code', $body)) {
            throw new CreateInviteFailedException();
        }

        return sprintf('%s/%s', config('services.discord.domain'), $body['code']);
    }
}
