<?php

declare(strict_types=1);

namespace App\Services\FileInfo;

use App\Models\Attachment;
use App\Services\Service;
use Generator;
use Illuminate\Support\LazyCollection;
use ZipArchive;

class ZipArchiveParser extends Service
{
    public function __construct(
        private readonly ZipArchive $zipArchive,
    ) {
    }

    /**
     * テキスト系ファイルをパースする(dat,tab,readme).
     *
     * @return LazyCollection<string, string>
     */
    public function parseTextContent(Attachment $attachment): LazyCollection
    {
        /** @var Generator<string, string> */
        $fn = function () use ($attachment): Generator {
            try {
                $this->zipArchive->open($attachment->full_path);

                for ($i = 0; $i < $this->zipArchive->numFiles; $i++) {
                    $stat = $this->zipArchive->statIndex($i, ZipArchive::FL_ENC_RAW);
                    if ($stat) {
                        $name = $stat['name'];
                        $text = $this->zipArchive->getFromIndex($stat['index']);
                        if ($name && $text) {
                            yield $this->convert($name) => $this->convert($text);
                        }
                    }
                }
            } finally {
                $this->zipArchive->close();
            }
        };

        return LazyCollection::make($fn);
    }

    private function convert(string $str): string
    {
        $detected = mb_detect_encoding($str, mb_list_encodings());

        return mb_convert_encoding($str, 'UTF-8', $detected ?: 'UTF-8');
    }
}
