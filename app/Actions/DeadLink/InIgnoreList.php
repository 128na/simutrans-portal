<?php

declare(strict_types=1);

namespace App\Actions\DeadLink;

class InIgnoreList
{
    private const array IGNORE_LIST = [
        'getuploader.com',
        'wikiwiki.jp',
    ];

    public function __invoke(string $url): bool
    {
        foreach (self::IGNORE_LIST as $domain) {
            if (mb_stripos($url, $domain) !== false) {
                return true;
            }
        }

        return false;
    }
}
