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

    public function __construct(private readonly Attachment $attachment) {}

    public function handle(FileInfoService $fileInfoService): void
    {
        $originalName = (string) $this->attachment->original_name;
        $lowerName = strtolower($originalName);

        // Optimize extension checks (single strtolower call)
        if (str_ends_with($lowerName, 'zip')) {
            $fileInfoService->updateOrCreateFromZip($this->attachment);
        } elseif (str_ends_with($lowerName, 'pak')) {
            $fileInfoService->updateOrCreateFromPak($this->attachment);
        }
    }
}
