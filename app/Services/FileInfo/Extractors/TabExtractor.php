<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors;

class TabExtractor implements Extractor
{
    #[\Override]
    public function isText(): bool
    {
        return true;
    }

    #[\Override]
    public function getKey(): string
    {
        return 'tabs';
    }

    #[\Override]
    public function isTarget(string $filename): bool
    {
        return str_ends_with($filename, '.tab');
    }

    /**
     * tabテキストからアドオン名と翻訳名を抽出する.
     *
     * @return string[]
     */
    #[\Override]
    public function extract(string $tab): array
    {
        $tabs = explode("\n", str_replace(["\r\n", "\r"], "\n", $tab));

        $translate = [];

        /** @var string|null */
        $line = null;
        foreach ($tabs as $tab) {
            if (str_starts_with($tab, '§')) {
                continue;
            }

            if (str_starts_with($tab, '#')) {
                continue;
            }

            if (is_null($line)) {
                $line = $tab;
            } else {
                $translate[$line] = $tab;
                $line = null;
            }
        }

        return $translate;
    }
}
