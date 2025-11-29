<?php

declare(strict_types=1);

namespace App\Services\FileInfo;

use App\Models\Attachment;
use Generator;
use Illuminate\Support\LazyCollection;
use ZipArchive;

final readonly class ZipArchiveParser
{
    public function __construct(
        private ZipArchive $zipArchive,
    ) {}

    /**
     * すべてのファイルをパースする.
     *
     * @return LazyCollection<string, array{content: string, is_binary: bool}>
     */
    public function parseContent(Attachment $attachment): LazyCollection
    {
        /** @var \Closure(): Generator<string, array{content: string, is_binary: bool}, mixed, void> */
        $fn = function () use ($attachment): Generator {
            try {
                $this->zipArchive->open($attachment->full_path);

                for ($i = 0; $i < $this->zipArchive->numFiles; $i++) {
                    $stat = $this->zipArchive->statIndex($i, ZipArchive::FL_ENC_RAW);
                    if ($stat) {
                        $name = $stat['name'];
                        $content = $this->zipArchive->getFromIndex($stat['index']);
                        if ($name && $content !== false && $content !== '') {
                            $isBinary = $this->isBinaryFile($name);
                            yield $this->convert($name) => [
                                'content' => $isBinary ? $content : $this->convert($content),
                                'is_binary' => $isBinary,
                            ];
                        }
                    }
                }
            } finally {
                $this->zipArchive->close();
            }
        };

        return LazyCollection::make($fn);
    }

    private function isBinaryFile(string $filename): bool
    {
        return str_ends_with(strtolower($filename), '.pak');
    }

    private function convert(string $str): string
    {
        // Fast path: Check if already valid UTF-8
        if (mb_check_encoding($str, 'UTF-8')) {
            return $str;
        }

        // Limited encoding detection (most common in Simutrans addons)
        $detected = mb_detect_encoding($str, ['UTF-8', 'SJIS', 'EUC-JP', 'ISO-8859-1'], true);
        $result = mb_convert_encoding($str, 'UTF-8', $detected ?: 'UTF-8');

        return $result === false ? $str : $result;
    }
}
