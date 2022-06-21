<?php

namespace App\Services\FileInfo;

use App\Models\Attachment;
use App\Services\Service;
use Illuminate\Support\LazyCollection;
use ZipArchive;

class ZipfileParser extends Service
{
    public function __construct(
        private ZipArchive $zipArchive,
    ) {
    }

    /**
     * テキスト系ファイルをパースする(dat,tab,readme).
     *
     * @return LazyCollection<string>
     */
    public function parseTextContent(Attachment $attachment): LazyCollection
    {
        return LazyCollection::make(function () use ($attachment) {
            try {
                $this->zipArchive->open($attachment->full_path);

                for ($i = 0; $i < $this->zipArchive->numFiles; ++$i) {
                    $stat = $this->zipArchive->statIndex($i);
                    $name = $stat['name'];
                    if ($this->isParseTarget($name)) {
                        $text = $this->zipArchive->getFromIndex($stat['index']);
                        yield $name => $this->removeBom($text);
                    }
                }
            } finally {
                $this->zipArchive->close();
            }
        });
    }

    private function removeBom(string $text): string
    {
        $bom = pack('H*', 'EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);

        return $text;
    }

    private function isParseTarget(string $filename): bool
    {
        return $this->isDatFile($filename)
            || $this->isTabFile($filename);
    }

    public function isDatFile(string $filename): bool
    {
        return str_ends_with($filename, '.dat');
    }

    public function isTabFile(string $filename): bool
    {
        return str_ends_with($filename, '.tab');
    }

    /**
     * datテキストからアドオン名を抽出する.
     *
     * @return string[]
     */
    public function extractNames(string $dat): array
    {
        preg_match_all('/[\s^]name\=(.*)\s/i', $dat, $matches);

        return array_map('trim', $matches[1] ?? []);
    }

    /**
     * datテキストからアドオン名を抽出する.
     *
     * @return string[]
     */
    public function extractTranslate(string $tab): array
    {
        $tabs = explode("\n", str_replace(["\r\n", "\r"], "\n", $tab));

        $translate = [];

        /** @var string|null */
        $line = null;
        foreach ($tabs as $text) {
            $text = trim($text);

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
