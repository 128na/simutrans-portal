<?php

declare(strict_types=1);

namespace App\Actions\DeadLink;

final class InIgnoreList
{
    private const IGNORE_LIST = [
        'getuploader.com',
    ];

    public function __invoke(string $url): bool
    {
        foreach (self::IGNORE_LIST as $domain) {
            if (stripos($url, $domain) !== false) {
                return true;
            }
        }

        return false;
    }
}
