<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors;

use App\Services\Service;

class DatExtractor extends Service implements Extractor
{
    public function isText(): bool
    {
        return true;
    }

    public function getKey(): string
    {
        return 'dats';
    }

    public function isTarget(string $filename): bool
    {
        return str_ends_with($filename, '.dat');
    }

    /**
     * datテキストからアドオン名を抽出する.
     *
     * @return string[]
     */
    public function extract(string $dat): array
    {
        preg_match_all('/[\s^]name\=(.*)\s/i', $dat, $matches);

        return array_map(function ($text) {
            return $text;
        }, $matches[1] ?? []);
    }
}
