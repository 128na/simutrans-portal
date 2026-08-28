<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Backup;

use App\Actions\Backup\SyncUserUploads;
use Illuminate\Support\Facades\Process;
use RuntimeException;
use Tests\Feature\TestCase;

class SyncUserUploadsTest extends TestCase
{
    private const array EXPECTED_FLAGS = ['--max-duration', '25s', '--cutoff-mode', 'SOFT', '-v'];

    public function test_runs_rclone_copy_to_dropbox_and_local_backup(): void
    {
        Process::fake();

        (new SyncUserUploads)();

        Process::assertRan(fn ($process) => $process->command === [
            config('rclone.binary_path'),
            'copy',
            config('rclone.uploads_source'),
            config('rclone.uploads_remote'),
            ...self::EXPECTED_FLAGS,
        ]);

        Process::assertRan(fn ($process) => $process->command === [
            config('rclone.binary_path'),
            'copy',
            config('rclone.uploads_source'),
            config('rclone.uploads_local_backup'),
            ...self::EXPECTED_FLAGS,
        ]);
    }

    public function test_returns_total_transferred_count_parsed_from_rclone_stats(): void
    {
        Process::fake([
            '*' => Process::result(output: <<<'OUTPUT'
                Transferred:   	    3.652 MiB / 2.322 GiB, 0%, 311.564 KiB/s, ETA 2h10m3s
                Errors:                 0
                Checks:               211 / 211, 100%, Listed 3096
                Transferred:           16 / 2558, 1%
                Elapsed time:        12.7s
                OUTPUT),
        ]);

        $transferred = (new SyncUserUploads)();

        $this->assertSame(32, $transferred);
    }

    public function test_does_not_throw_when_rclone_stops_due_to_max_duration(): void
    {
        Process::fake([
            '*' => Process::result(errorOutput: 'max transfer duration reached as set by --max-duration', exitCode: 10),
        ]);

        (new SyncUserUploads)();

        $this->expectNotToPerformAssertions();
    }

    public function test_throws_when_rclone_exits_with_an_unexpected_failure(): void
    {
        Process::fake([
            '*' => Process::result(errorOutput: 'boom', exitCode: 1),
        ]);

        $this->expectException(RuntimeException::class);

        (new SyncUserUploads)();
    }
}
