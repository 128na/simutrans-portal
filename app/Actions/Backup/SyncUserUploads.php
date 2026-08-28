<?php

declare(strict_types=1);

namespace App\Actions\Backup;

use Illuminate\Support\Facades\Process;
use RuntimeException;

class SyncUserUploads
{
    private const int TIMEOUT_SECONDS = 21600;

    public function __invoke(): void
    {
        foreach ($this->destinations() as $destination) {
            $this->copyTo($destination);
        }
    }

    /**
     * @return array<int, string>
     */
    private function destinations(): array
    {
        return [
            config('rclone.uploads_remote'),
            config('rclone.uploads_local_backup'),
        ];
    }

    private function copyTo(string $destination): void
    {
        $result = Process::timeout(self::TIMEOUT_SECONDS)->run([
            config('rclone.binary_path'),
            'copy',
            config('rclone.uploads_source'),
            $destination,
        ]);

        if ($result->failed()) {
            throw new RuntimeException("rclone copy to {$destination} failed: ".$result->errorOutput());
        }
    }
}
