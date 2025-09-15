<?php

declare(strict_types=1);

namespace App\Actions\DeadLink;

final class GetHeaders
{
    /**
     * @return array<string>
     */
    public function __invoke(string $url): array
    {
        return (@get_headers($url)) ?: [];
    }
}
