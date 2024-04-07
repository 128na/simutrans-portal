<?php

declare(strict_types=1);

namespace App\Services\Article;

final class GetHeadersHandler
{
    /**
     * @return array<string>
     */
    public function getHeaders(string $url): array
    {
        return @get_headers($url) ?: [];
    }
}
