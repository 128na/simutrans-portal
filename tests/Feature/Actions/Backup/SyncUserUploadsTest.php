<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Backup;

use App\Actions\Backup\SyncUserUploads;
use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Process\Exceptions\ProcessTimedOutException;
use Illuminate\Process\PendingProcess;
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

    public function test_remote_failure_still_attempts_local_backup_and_reports_failure(): void
    {
        $this->travelTo(now()->setSecond(0));
        config(['rclone.uploads_remote' => 'remote:uploads', 'rclone.uploads_local_backup' => 'local-backup']);
        Process::fake([
            '*' => Process::sequence()
                ->push(Process::result(errorOutput: 'remote unavailable', exitCode: 1))
                ->push(Process::result(output: 'Transferred: 2 / 2, 100%')),
        ]);

        try {
            (new SyncUserUploads)();
            $this->fail('Expected failure after trying both backup destinations.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('remote:uploads', $exception->getMessage());
            $this->assertStringContainsString('remote unavailable', $exception->getMessage());
            $this->assertInstanceOf(RuntimeException::class, $exception->getPrevious());
        }

        Process::assertRanTimes(fn ($process) => $process->command[3] === 'remote:uploads', 1);
        Process::assertRanTimes(fn ($process) => $process->command[3] === 'local-backup', 1);
    }

    public function test_local_failure_still_attempts_remote_backup_in_reverse_order(): void
    {
        $this->travelTo(now()->setSecond(1));
        config(['rclone.uploads_remote' => 'remote:uploads', 'rclone.uploads_local_backup' => 'local-backup']);
        Process::fake([
            '*' => Process::sequence()
                ->push(Process::result(errorOutput: 'local unavailable', exitCode: 1))
                ->push(Process::result()),
        ]);

        try {
            (new SyncUserUploads)();
            $this->fail('Expected failure after trying both backup destinations.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('local-backup', $exception->getMessage());
            $this->assertStringContainsString('local unavailable', $exception->getMessage());
        }

        Process::assertRanTimes(fn ($process) => $process->command[3] === 'local-backup', 1);
        Process::assertRanTimes(fn ($process) => $process->command[3] === 'remote:uploads', 1);
    }

    public function test_both_destination_failures_are_reported_after_both_attempts(): void
    {
        $this->travelTo(now()->setSecond(0));
        config(['rclone.uploads_remote' => 'remote:uploads', 'rclone.uploads_local_backup' => 'local-backup']);
        Process::fake([
            '*' => Process::sequence()
                ->push(Process::result(errorOutput: 'remote unavailable', exitCode: 1))
                ->push(Process::result(errorOutput: 'disk full', exitCode: 1)),
        ]);

        try {
            (new SyncUserUploads)();
            $this->fail('Expected both failures to be reported.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('remote unavailable', $exception->getMessage());
            $this->assertStringContainsString('disk full', $exception->getMessage());
        }

        Process::assertRanTimes(fn ($process) => $process->command[3] === 'local-backup', 1);
        Process::assertRanTimes(fn ($process) => $process->command[3] === 'remote:uploads', 1);
    }

    public function test_process_timeout_still_attempts_other_destination_and_preserves_cause(): void
    {
        $this->travelTo(now()->setSecond(0));
        config(['rclone.uploads_remote' => 'remote:uploads', 'rclone.uploads_local_backup' => 'local-backup']);
        $nativeProcess = new \Symfony\Component\Process\Process(['rclone']);
        $nativeProcess->setTimeout(600);
        $timeout = new ProcessTimedOutException(
            new \Symfony\Component\Process\Exception\ProcessTimedOutException($nativeProcess, \Symfony\Component\Process\Exception\ProcessTimedOutException::TYPE_GENERAL),
            Process::result(),
        );
        $attempted = [];
        Process::fake([
            '*' => function (PendingProcess $process) use (&$attempted, $timeout): ProcessResult {
                $attempted[] = $process->command[3];
                $this->assertSame(600, $process->timeout);
                if ($process->command[3] === 'remote:uploads') {
                    throw $timeout;
                }

                return Process::result();
            },
        ]);

        try {
            (new SyncUserUploads)();
            $this->fail('Expected timeout to be reported after both attempts.');
        } catch (RuntimeException $exception) {
            $this->assertSame($timeout, $exception->getPrevious());
            $this->assertStringContainsString('remote:uploads', $exception->getMessage());
        }

        $this->assertSame(['remote:uploads', 'local-backup'], $attempted);
    }
}
