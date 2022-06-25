<?php

namespace App\Services\FileInfo\Extractors;

use App\Services\Service;
use HTMLPurifier;

class ReadmeExtractor extends Service implements Extractor
{
    public function __construct(private HTMLPurifier $purifier)
    {
    }

    public function isText(): bool
    {
        return true;
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
        return [$this->purifier->purify($text)];
    }
}
