<?php

declare(strict_types=1);

namespace App\Services\FileInfo;

use App\Models\Attachment;
use Generator;
use Illuminate\Support\LazyCollection;
use ZipArchive;

class ZipArchiveParser
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
            $opened = false;
            try {
                $result = $this->zipArchive->open($attachment->full_path);
                if ($result !== true) {
                    throw new \Exception('Failed to open zip file: '.$this->getZipError($result));
                }

                $opened = true;

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
                if ($opened) {
                    $this->zipArchive->close();
                }
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

        if ($result === false) {
            // Conversion failed, try to sanitize
            return mb_convert_encoding($str, 'UTF-8', 'UTF-8');
        }

        // Verify converted string is valid UTF-8
        if (! mb_check_encoding($result, 'UTF-8')) {
            // If still invalid, force sanitize by converting UTF-8 to UTF-8 (drops invalid bytes)
            return mb_convert_encoding($result, 'UTF-8', 'UTF-8');
        }

        return $result;
    }

    private function getZipError(int $code): string
    {
        return match ($code) {
            ZipArchive::ER_EXISTS => 'File already exists',
            ZipArchive::ER_INCONS => 'Zip archive inconsistent',
            ZipArchive::ER_INVAL => 'Invalid argument',
            ZipArchive::ER_MEMORY => 'Malloc failure',
            ZipArchive::ER_NOENT => 'No such file',
            ZipArchive::ER_NOZIP => 'Not a zip archive',
            ZipArchive::ER_OPEN => "Can't open file",
            ZipArchive::ER_READ => 'Read error',
            ZipArchive::ER_SEEK => 'Seek error',
            default => sprintf('Unknown error (%s)', $code),
        };
    }
}
