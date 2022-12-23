<?php

namespace App\Jobs\Attachments;

use App\Models\Attachment;
use App\Repositories\AttachmentRepository;
use App\Repositories\CompressedImageRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class JobCompressImage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private ?CompressedImageRepository $compressedImageRepository;

    public function handle(
        AttachmentRepository $attachmentRepository,
        CompressedImageRepository $compressedImageRepository
    ) {
        $this->compressedImageRepository = $compressedImageRepository;

        foreach ($attachmentRepository->cursorCheckCompress() as $attachment) {
            if ($this->shouldCompress($attachment)) {
                try {
                    $this->compress($attachment);
                } catch (\Throwable $e) {
                    logger()->error($e->getMessage());
                }
            }
        }
    }

    private function shouldCompress(Attachment $attachment): bool
    {
        if (! $attachment->path_exists) {
            logger()->warning('missing path', [$attachment->full_path]);

            return false;
        }
        if (! $attachment->is_png) {
            return false;
        }
        if ($this->compressedImageRepository->existsByPath($attachment->path)) {
            return false;
        }

        return true;
    }

    private function compress(Attachment $attachment)
    {
        \Tinify\setKey(config('app.tinypng_api_key'));

        $path = $attachment->path;
        $backup_path = $path.'.bak';
        $disk = Storage::disk('public');
        try {
            $disk->copy($path, $backup_path);
            $source = \Tinify\fromFile($disk->path($path));
            $source->toFile($disk->path($path));

            $this->compressedImageRepository->store([
                'path' => $path,
            ]);

            $disk->delete($backup_path);
        } catch (\Throwable $e) {
            $disk->delete($path);
            $disk->move($backup_path, $path);
            throw $e;
        }
    }
}
