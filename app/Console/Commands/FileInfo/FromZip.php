<?php

namespace App\Console\Commands\FileInfo;

use App\Repositories\AttachmentRepository;
use App\Services\FileInfo\FileInfoService;
use Illuminate\Console\Command;
use Throwable;

class FromZip extends Command
{
    protected $signature = 'fileinfo:zip';

    protected $description = 'Update zip file info';

    public function __construct(
        private AttachmentRepository $attachmentRepository,
        private FileInfoService $fileInfoService,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $cursor = $this->attachmentRepository->cursorZipFileAttachment();

        foreach ($cursor as $attachment) {
            try {
                $this->fileInfoService->updateOrCreateFromZip($attachment);
            } catch (Throwable $e) {
                report($e);
                $this->error($e->getMessage());
            }
        }

        return 0;
    }
}
