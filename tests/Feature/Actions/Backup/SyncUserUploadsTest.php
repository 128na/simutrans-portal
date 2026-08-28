<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Backup;

use App\Actions\Backup\SyncUserUploads;
use Illuminate\Support\Facades\Process;
use RuntimeException;
use Tests\Feature\TestCase;

class SyncUserUploadsTest extends TestCase
{
    public function test_runs_rclone_copy_to_dropbox_and_local_backup(): void
    {
        Process::fake();

        (new SyncUserUploads)();

        Process::assertRan(fn ($process) => $process->command === [
            config('rclone.binary_path'),
            'copy',
            config('rclone.uploads_source'),
            config('rclone.uploads_remote'),
        ]);

        Process::assertRan(fn ($process) => $process->command === [
            config('rclone.binary_path'),
            'copy',
            config('rclone.uploads_source'),
            config('rclone.uploads_local_backup'),
        ]);
    }

    public function test_throws_when_rclone_exits_with_failure(): void
    {
        Process::fake([
            '*' => Process::result(errorOutput: 'boom', exitCode: 1),
        ]);

        $this->expectException(RuntimeException::class);

        (new SyncUserUploads)();
    }
}
