<?php

declare(strict_types=1);

namespace App\Actions\DeadLink;

class GetHeaders
{
    public function __construct(
        private readonly NormalizeUrl $normalizeUrl,
    ) {}

    /**
     * @return array<string>|null 接続自体ができなかった場合は null
     */
    public function __invoke(string $url): ?array
    {
        $context = stream_context_create(['http' => ['timeout' => 5]]);
        $headers = @get_headers(($this->normalizeUrl)($url), false, $context);

        return $headers === false ? null : $headers;
    }
}
