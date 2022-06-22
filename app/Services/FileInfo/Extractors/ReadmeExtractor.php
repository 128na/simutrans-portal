<?php

namespace App\Services\FileInfo\Extractors;

use App\Services\FileInfo\TextService;
use App\Services\Service;

class ReadmeExtractor extends Service implements Extractor
{
    public function __construct(
        private TextService $textService
    ) {
    }

    public function getKey(): string
    {
        return 'readmes';
    }

    public function isTarget(string $filename): bool
    {
        return str_contains($filename, 'readme');
    }

    /**
     * tabテキストからアドオン名と翻訳名を抽出する.
     *
     * @return string[]
     */
    public function extract(string $text): array
    {
        return [$this->textService->encoding($text)];
    }
}
