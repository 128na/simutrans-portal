<?php

declare(strict_types=1);

namespace App\Services\Article;

use App\Services\Service;

class GetHeadersHandler extends Service
{
    /**
     * @return array<string>
     */
    public function getHeaders(string $url): array
    {
        return @get_headers($url) ?: [];
    }
}
