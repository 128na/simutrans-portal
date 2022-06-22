<?php

namespace App\Services\FileInfo\Extractors;

use App\Services\FileInfo\TextService;
use App\Services\Service;

class DatExtractor extends Service implements Extractor
{
    public function __construct(
        private TextService $textService
    ) {
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
        $dat = $this->textService->removeBom($dat);
        preg_match_all('/[\s^]name\=(.*)\s/i', $dat, $matches);

        return array_map(function ($text) {
            return $this->textService->encoding($text);
        }, $matches[1] ?? []);
    }
}
