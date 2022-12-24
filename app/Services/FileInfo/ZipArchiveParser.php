<?php

namespace App\Services\FileInfo;

use Generator;
use ZipArchive;
use App\Services\Service;
use App\Models\Attachment;
use Illuminate\Support\LazyCollection;

class ZipArchiveParser extends Service
{
    public function __construct(
        private ZipArchive $zipArchive,
    ) {
    }

    /**
     * テキスト系ファイルをパースする(dat,tab,readme).
     * @return LazyCollection<string, string>
     */
    public function parseTextContent(Attachment $attachment): LazyCollection
    {
        /** @var Generator<string, string> */
        $fn = function () use ($attachment):Generator {
            try {
                $this->zipArchive->open($attachment->full_path);

                for ($i = 0; $i < $this->zipArchive->numFiles; $i++) {
                    $stat = $this->zipArchive->statIndex($i, ZipArchive::FL_ENC_RAW);
                    $name = $stat['name'];
                    yield $name => $this->zipArchive->getFromIndex($stat['index']);
                }
            } finally {
                $this->zipArchive->close();
            }
        };
        return LazyCollection::make($fn);
    }
}
