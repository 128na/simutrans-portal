<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors;

use HTMLPurifier;

final readonly class ReadmeExtractor implements Extractor
{
    public function __construct(
        private HTMLPurifier $htmlPurifier,
    ) {}

    #[\Override]
    public function isText(): bool
    {
        return true;
    }

    #[\Override]
    public function getKey(): string
    {
        return 'readmes';
    }

    #[\Override]
    public function isTarget(string $filename): bool
    {
        return (
            str_contains($filename, 'readme')
            && (str_contains($filename, '.txt') || str_contains($filename, '.md') || str_contains($filename, '.html'))
        );
    }

    /**
     * tabテキストからアドオン名と翻訳名を抽出する.
     *
     * @return string[]
     */
    #[\Override]
    public function extract(string $text): array
    {
        return [$this->htmlPurifier->purify($text)];
    }
}
