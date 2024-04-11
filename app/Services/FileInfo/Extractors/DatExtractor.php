<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors;

final class DatExtractor implements Extractor
{
    #[\Override]
    public function isText(): bool
    {
        return true;
    }

    #[\Override]
    public function getKey(): string
    {
        return 'dats';
    }

    #[\Override]
    public function isTarget(string $filename): bool
    {
        return str_ends_with($filename, '.dat');
    }

    /**
     * datテキストからアドオン名を抽出する.
     *
     * @return string[]
     */
    #[\Override]
    public function extract(string $dat): array
    {
        preg_match_all('/[\s^]name\=(.*)\s/i', $dat, $matches);

        return array_map(fn ($text): string => trim((string) $text), $matches[1] ?? []);
    }
}
