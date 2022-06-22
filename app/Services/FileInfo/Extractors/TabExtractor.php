<?php

namespace App\Services\FileInfo\Extractors;

use App\Services\FileInfo\TextService;
use App\Services\Service;

class TabExtractor extends Service implements Extractor
{
    public function __construct(
        private TextService $textService
    ) {
    }

    public function getKey(): string
    {
        return 'tabs';
    }

    public function isTarget(string $filename): bool
    {
        return str_ends_with($filename, '.tab');
    }

    /**
     * tabテキストからアドオン名と翻訳名を抽出する.
     *
     * @return string[]
     */
    public function extract(string $tab): array
    {
        $tab = $this->textService->removeBom($tab);
        $tabs = explode("\n", str_replace(["\r\n", "\r"], "\n", $tab));

        $translate = [];

        /** @var string|null */
        $line = null;
        foreach ($tabs as $text) {
            $text = $this->textService->encoding($text);

            if (str_starts_with($text, '§') || str_starts_with($text, '#')) {
                continue;
            }
            if (is_null($line)) {
                $line = $text;
            } else {
                $translate[$line] = $text;
                $line = null;
            }
        }

        return $translate;
    }
}
