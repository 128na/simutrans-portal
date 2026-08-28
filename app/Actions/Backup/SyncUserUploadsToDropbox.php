<?php

declare(strict_types=1);

namespace App\Actions\Backup;

use Illuminate\Support\Facades\Process;
use RuntimeException;

class SyncUserUploadsToDropbox
{
    private const int TIMEOUT_SECONDS = 3600;

    public function __invoke(): void
    {
        $result = Process::timeout(self::TIMEOUT_SECONDS)->run([
            config('rclone.binary_path'),
            'copy',
            config('rclone.uploads_source'),
            config('rclone.uploads_remote'),
        ]);

        if ($result->failed()) {
            throw new RuntimeException('rclone copy failed: '.$result->errorOutput());
        }
    }
}
