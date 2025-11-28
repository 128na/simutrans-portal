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
use Illuminate\Support\Facades\Log;

final class UpdateFileInfo implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param  int|null  $maxSizeMb  Maximum file size in MB (null = unlimited)
     */
    public function __construct(
        private readonly Attachment $attachment,
        private readonly ?int $maxSizeMb = null,
    ) {}

    public function handle(FileInfoService $fileInfoService): void
    {
        $originalName = (string) $this->attachment->original_name;
        $lowerName = strtolower($originalName);

        // Skip large files if max size is specified
        if ($this->maxSizeMb !== null) {
            $maxSize = $this->maxSizeMb * 1024 * 1024;
            if ($this->attachment->size > $maxSize) {
                Log::info('Skipping large file', [
                    'attachment_id' => $this->attachment->id,
                    'filename' => $originalName,
                    'size_mb' => round($this->attachment->size / 1024 / 1024, 2),
                    'max_size_mb' => $this->maxSizeMb,
                ]);

                return;
            }
        }

        $startTime = microtime(true);

        // Optimize extension checks (single strtolower call)
        if (str_ends_with($lowerName, 'zip')) {
            $fileInfoService->updateOrCreateFromZip($this->attachment);
        } elseif (str_ends_with($lowerName, 'pak')) {
            $fileInfoService->updateOrCreateFromPak($this->attachment);
        }

        $duration = microtime(true) - $startTime;

        if ($duration > 30) {
            Log::warning('Slow pak/zip file processing detected', [
                'attachment_id' => $this->attachment->id,
                'filename' => $originalName,
                'duration_seconds' => round($duration, 2),
            ]);
        }
    }
}
