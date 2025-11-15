<?php

declare(strict_types=1);

namespace App\Jobs\Attachments;

use App\Models\Attachment;
use App\Services\FileInfo\FileInfoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class UpdateFileInfo implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly Attachment $attachment,
    ) {}

    public function handle(FileInfoService $fileInfoService): void
    {
        if (str_ends_with((string) $this->attachment->original_name, 'zip')) {
            $fileInfoService->updateOrCreateFromZip($this->attachment);
        }

        if (str_ends_with((string) $this->attachment->original_name, 'pak')) {
            $fileInfoService->updateOrCreateFromPak($this->attachment);
        }
    }
}
