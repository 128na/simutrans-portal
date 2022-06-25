<?php

namespace App\Services\FileInfo;

use App\Models\Attachment;
use App\Services\Service;
use Illuminate\Support\LazyCollection;
use ZipArchive;

class ZipArchiveParser extends Service
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
                    $stat = $this->zipArchive->statIndex($i, ZipArchive::FL_ENC_RAW);
                    $name = $stat['name'];
                    yield $name => $this->zipArchive->getFromIndex($stat['index']);
                }
            } finally {
                $this->zipArchive->close();
            }
        });
    }
}
