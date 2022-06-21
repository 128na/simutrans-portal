<?php

namespace App\Console\Commands\FileInfo;

use App\Repositories\Attachment\FileInfoRepository;
use App\Repositories\AttachmentRepository;
use App\Services\FileInfo\ZipfileParser;
use Illuminate\Console\Command;
use Throwable;

class Update extends Command
{
    protected $signature = 'fileinfo:update';

    protected $description = 'Update zip file info';

    public function __construct(
        private AttachmentRepository $attachmentRepository,
        private FileInfoRepository $fileInfoRepository,
        private ZipfileParser $zipfileParser,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $cursor = $this->attachmentRepository->cursorNeedUpdateFileInfoAttachment();

        foreach ($cursor as $attachment) {
            try {
                $data = [
                    'addons' => [],
                    'tabs' => [],
                ];

                $contentCursor = $this->zipfileParser->parseTextContent($attachment);
                foreach ($contentCursor as $filename => $text) {
                    if ($this->zipfileParser->isDatFile($filename)) {
                        $data['addons'][] = $this->zipfileParser->extractNames($text);
                    }
                    if ($this->zipfileParser->isTabFile($filename)) {
                        $data['tabs'][] = $this->zipfileParser->extractTranslate($text);
                    }
                }

                $this->fileInfoRepository->store([
                    'attachment_id' => $attachment->id,
                    'data' => $data,
                ]);
            } catch (Throwable $e) {
                report($e);
                $this->error($e->getMessage());
            }
        }

        return 0;
    }
}
