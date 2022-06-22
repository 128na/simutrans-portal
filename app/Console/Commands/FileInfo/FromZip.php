<?php

namespace App\Console\Commands\FileInfo;

use App\Repositories\Attachment\FileInfoRepository;
use App\Repositories\AttachmentRepository;
use App\Services\FileInfo\Extractors\Extractor;
use App\Services\FileInfo\InvalidEncodingException;
use App\Services\FileInfo\ZipArchiveParser;
use Illuminate\Console\Command;
use Throwable;

class FromZip extends Command
{
    protected $signature = 'fileinfo:zip';

    protected $description = 'Update zip file info';

    /**
     * @param Extractor[] $extractors
     */
    public function __construct(
        private AttachmentRepository $attachmentRepository,
        private FileInfoRepository $fileInfoRepository,
        private ZipArchiveParser $zipArchiveParser,
        private array $extractors,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $cursor = $this->attachmentRepository->cursorZipFileAttachment();

        foreach ($cursor as $attachment) {
            try {
                $contentCursor = $this->zipArchiveParser->parseTextContent($attachment);
                $data = [];
                foreach ($contentCursor as $filename => $text) {
                    foreach ($this->extractors as $extractor) {
                        if ($extractor->isTarget($filename)) {
                            $data[$extractor->getKey()][$filename] = $extractor->extract($text);
                        }
                    }
                }

                $this->fileInfoRepository->updateOrCreate(['attachment_id' => $attachment->id], ['data' => $data]);
            } catch (InvalidEncodingException $e) {
                $this->fileInfoRepository->updateOrCreate(['attachment_id' => $attachment->id], ['data' => []]);
            } catch (Throwable $e) {
                report($e);
                $this->error($e->getMessage());
            }
        }

        return 0;
    }
}
