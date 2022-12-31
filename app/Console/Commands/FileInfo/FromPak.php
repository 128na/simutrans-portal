<?php

declare(strict_types=1);

namespace App\Console\Commands\FileInfo;

use App\Repositories\AttachmentRepository;
use App\Services\FileInfo\FileInfoService;
use Illuminate\Console\Command;
use Throwable;

class FromPak extends Command
{
    protected $signature = 'fileinfo:pak';

    protected $description = 'Update pak file info';

    public function __construct(
        private AttachmentRepository $attachmentRepository,
        private FileInfoService $fileInfoService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        try {
            $cursor = $this->attachmentRepository->cursorPakFileAttachment();

            foreach ($cursor as $attachment) {
                try {
                    $this->fileInfoService->updateOrCreateFromPak($attachment);
                } catch (Throwable $e) {
                    report($e);
                    $this->error($e->getMessage());
                }
            }
        } catch (Throwable $e) {
            report($e);

            return 1;
        }

        return 0;
    }
}
