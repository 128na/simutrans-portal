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

                $maxEntryBytes = $this->limit('max_entry_bytes', 64 * 1024 * 1024);
                $maxTotalBytes = $this->limit('max_total_bytes', 256 * 1024 * 1024);
                $maxEntries = $this->limit('max_entries', 10000);
                if ($this->zipArchive->numFiles > $maxEntries) {
                    throw new ZipResourceLimitException('ZIP entry count exceeds metadata budget.');
                }

                $declaredTotal = 0;
                for ($i = 0; $i < $this->zipArchive->numFiles; $i++) {
                    $stat = $this->zipArchive->statIndex($i, ZipArchive::FL_ENC_RAW);
                    if (! $stat || ! isset($stat['size']) || ! is_int($stat['size']) || $stat['size'] < 0 || $stat['size'] > $maxEntryBytes || $stat['size'] > $maxTotalBytes - $declaredTotal) {
                        throw new ZipResourceLimitException('ZIP declared size exceeds metadata budget.');
                    }
                    $declaredTotal += $stat['size'];
                }

                $actualTotal = 0;

                for ($i = 0; $i < $this->zipArchive->numFiles; $i++) {
                    $stat = $this->zipArchive->statIndex($i, ZipArchive::FL_ENC_RAW);
                    if (! $stat) {
                        throw new \RuntimeException('Failed to inspect ZIP entry.');
                    }
                    $name = $stat['name'];
                    $remainingBytes = min($maxEntryBytes, $maxTotalBytes - $actualTotal);
                    $content = $this->zipArchive->getFromIndex($i, $remainingBytes + 1);
                    if ($content === false) {
                        throw new \RuntimeException('Failed to read ZIP entry.');
                    }
                    $actualBytes = strlen($content);
                    if ($actualBytes > $remainingBytes) {
                        throw new ZipResourceLimitException('ZIP expanded size exceeds metadata budget.');
                    }
                    $actualTotal += $actualBytes;
                    if ($name && $content !== '') {
                        $isBinary = $this->isBinaryFile($name);
                        yield $this->convert($name) => [
                            'content' => $isBinary ? $content : $this->convert($content),
                            'is_binary' => $isBinary,
                        ];
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
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return ! in_array($ext, ['dat', 'txt', 'tab', 'md'], true);
    }

    private function limit(string $key, int $default): int
    {
        $value = config('fileinfo.zip.'.$key, $default);

        return is_int($value) && $value > 0 && $value <= $default ? $value : $default;
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
