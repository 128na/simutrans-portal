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
        $prev = set_error_handler(static function (int $errno, string $errstr): bool {
            // convert warnings from get_headers into silence for this scope
            return true;
        });

        try {
            $raw = get_headers($url);
            $headers = $raw === false ? [] : $raw;
        } finally {
            if ($prev !== null) {
                set_error_handler($prev);
            } else {
                restore_error_handler();
            }
        }

        return $headers;
    }
}
