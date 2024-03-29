<?php

declare(strict_types=1);

namespace App\Services\Misskey;

use App\Services\Service;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * @see https://misskey-hub.net/docs/api/
 */
class MisskeyApiClient extends Service
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $token,
    ) {

    }

    /**
     * @see https://misskey-hub.net/docs/api/endpoints/notes/create.html
     */
    public function send(string $text): Response
    {
        return Http::withHeader('Content-Type', 'application/json')
            ->post($this->baseUrl.'/notes/create', [
                'i' => $this->token,
                'text' => $text,
            ]);
    }
}
