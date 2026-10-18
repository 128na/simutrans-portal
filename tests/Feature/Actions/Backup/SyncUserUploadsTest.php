<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Backup;

use App\Actions\Backup\SyncUserUploads;
use App\Actions\Backup\UploadBackupHealth;
use App\Exceptions\UploadBackupInterruptedException;
use Illuminate\Contracts\Process\InvokedProcess;
use Illuminate\Process\Exceptions\ProcessTimedOutException;
use Illuminate\Process\FakeProcessDescription;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Mockery;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Tests\Feature\TestCase;

class SyncUserUploadsTest extends TestCase
{
    /** @var list<array{event:string, context:array<string,mixed>}> */
    private array $records = [];

    /** @var list<string> */
    private array $successfulDestinations = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->mock(UploadBackupHealth::class)->shouldReceive('recordSuccess')->andReturnUsing(function (string $label): void {
            $this->successfulDestinations[] = $label;
        });
        $this->travelTo(now()->setSecond(0));
        config(['rclone.uploads_remote' => 'secret-remote:path', 'rclone.uploads_local_backup' => 'secret-local-path']);
        $logger = Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('info')->andReturnUsing(function (string $event, array $context): void {
            $this->records[] = ['event' => $event, 'context' => $context];
        });
        Log::shouldReceive('channel')->with('backup-sync')->andReturn($logger);
    }

    public function test_success_records_pid_safe_stats_and_shared_run_id_for_both_destinations(): void
    {
        Process::fake(['*' => Process::describe()->id(456)->errorOutput('{"level":"info","msg":"secret-token","stats":{"transfers":16,"bytes":123}}')]);
        $this->assertSame(32, (new SyncUserUploads)());
        $this->assertSame(['dropbox', 'local_backup'], $this->successfulDestinations);
        $this->assertCount(4, $this->records);
        $this->assertSame(['started', 'finished', 'started', 'finished'], array_column($this->records, 'event'));
        $runIds = array_unique(array_column(array_column($this->records, 'context'), 'run_id'));
        $this->assertCount(1, $runIds);
        $this->assertNotEmpty(reset($runIds));
        foreach ([0, 2] as $index) {
            $this->assertSame(456, $this->records[$index]['context']['pid']);
            $finished = $this->records[$index + 1]['context'];
            $this->assertSame('success', $finished['status']);
            $this->assertSame(0, $finished['exit_code']);
            $this->assertGreaterThanOrEqual(0, $finished['elapsed_seconds']);
            $this->assertNotEmpty($finished['started_at']);
        }
        $this->assertSame('dropbox', $this->records[0]['context']['destination']);
        $this->assertSame('local_backup', $this->records[2]['context']['destination']);
        Process::assertRanTimes(fn ($process) => $process->timeout === 600 && $process->quietly && array_slice($process->command, 4) === ['--max-duration', '25s', '--cutoff-mode', 'SOFT', '-v', '--use-json-log', '--stats', '10s'], 2);
        $this->assertSafeRecords();
    }

    public function test_duration_limit_is_normal_and_counts_last_valid_json_stats(): void
    {
        Process::fake(['*' => Process::describe()->errorOutput("{\"stats\":{\"transfers\":2}}\n{\"stats\":{\"transfers\":3}}\ninvalid")->exitCode(10)]);
        $this->assertSame(6, (new SyncUserUploads)());
        $this->assertSame([], $this->successfulDestinations);
        $this->assertSame('duration_limit', $this->records[1]['context']['status']);
        $this->assertSame(10, $this->records[1]['context']['exit_code']);
    }

    public function test_missing_stats_return_zero(): void
    {
        Process::fake(['*' => Process::result(errorOutput: 'secret-token')]);
        $this->assertSame(0, (new SyncUserUploads)());
        $this->assertSafeRecords();
    }

    public function test_first_failure_still_attempts_other_destination_in_both_orders(): void
    {
        foreach ([0, 1] as $second) {
            $this->records = [];
            $this->travelTo(now()->setSecond($second));
            Process::fake(['*' => Process::sequence()->push(Process::result(errorOutput: 'secret-token', exitCode: 1))->push(Process::result())]);
            $this->assertSafeFailure();
            $this->assertCount(4, $this->records);
            $this->assertSame($second === 0 ? 'dropbox' : 'local_backup', $this->records[0]['context']['destination']);
            $this->assertSame('failed', $this->records[1]['context']['status']);
            $this->assertSame('success', $this->records[3]['context']['status']);
            $this->assertSafeRecords();
        }
    }

    public function test_both_failures_are_reported_after_all_attempts(): void
    {
        Process::fake(['*' => Process::result(errorOutput: 'secret-token', exitCode: 1)]);
        try {
            (new SyncUserUploads)();
            $this->fail('Expected failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Upload backup synchronization failed [run_id='.$this->records[0]['context']['run_id'].']: dropbox, local_backup', $exception->getMessage());
            $this->assertNull($exception->getPrevious());
        }
        $this->assertCount(4, $this->records);
    }

    public function test_timeout_keeps_callback_stats_and_pid_and_still_runs_next_destination(): void
    {
        $native = new \Symfony\Component\Process\Process(['secret-token']);
        $native->setTimeout(600);
        $exception = new ProcessTimedOutException(new \Symfony\Component\Process\Exception\ProcessTimedOutException($native, \Symfony\Component\Process\Exception\ProcessTimedOutException::TYPE_GENERAL), Process::result(exitCode: -1));
        $this->fakeWaitFailure($exception);
        $this->assertSafeFailure();
        $this->assertSame('timeout', $this->records[1]['context']['status']);
        $this->assertSame(ProcessTimedOutException::class, $this->records[1]['context']['exception_class']);
        $this->assertFailedCallbackDiagnostics();
    }

    public function test_sigkill_keeps_callback_stats_and_is_distinguished_from_timeout(): void
    {
        $native = Mockery::mock(\Symfony\Component\Process\Process::class);
        $native->shouldReceive('getTermSignal')->andReturn(9);
        $this->fakeWaitFailure(new ProcessSignaledException($native));
        $this->assertInstanceOf(UploadBackupInterruptedException::class, $this->assertSafeFailure());
        $this->assertSame('sigkill', $this->records[1]['context']['status']);
        $this->assertSame(9, $this->records[1]['context']['signal']);
        $this->assertFailedCallbackDiagnostics();
    }

    public function test_logger_failure_does_not_interrupt_backups(): void
    {
        Log::shouldReceive('channel')->with('backup-sync')->andThrow(new RuntimeException('secret-token'));
        Process::fake();
        $this->assertSame(0, (new SyncUserUploads)());
        Process::assertRanTimes(fn () => true, 2);
    }

    public function test_start_failure_is_recorded_without_sensitive_error_and_other_destination_is_attempted(): void
    {
        $this->travelTo(now()->setSecond(0));
        $attempt = 0;
        Process::fake(['*' => function (PendingProcess $process) use (&$attempt): FakeProcessDescription {
            if (++$attempt === 1) {
                throw new RuntimeException('secret-token in command');
            }

            return Process::describe()->exitCode(0);
        }]);

        $this->assertSafeFailure();
        $this->assertSame(2, $attempt);
        $this->assertCount(4, $this->records);
        $this->assertSame('start_failed', $this->records[1]['context']['status']);
        $this->assertNull($this->records[1]['context']['pid']);
        $this->assertSame('success', $this->records[3]['context']['status']);
        $this->assertSafeRecords();
    }

    public function test_health_recording_failure_is_not_suppressed_and_other_destination_is_attempted(): void
    {
        $this->mock(UploadBackupHealth::class)->shouldReceive('recordSuccess')->twice()->andThrow(new RuntimeException('secret-token'));
        Process::fake();

        $this->assertNotInstanceOf(UploadBackupInterruptedException::class, $this->assertSafeFailure());

        Process::assertRanTimes(fn () => true, 2);
        $this->assertSame('success', $this->records[1]['context']['status']);
        $this->assertSame('success', $this->records[3]['context']['status']);
        $this->assertSafeRecords();
    }

    public function test_daily_diagnostics_retention_is_seven_days(): void
    {
        $this->assertSame('daily', config('logging.channels.backup-sync.driver'));
        $this->assertSame(7, config('logging.channels.backup-sync.days'));
        $this->assertSame(storage_path('logs/backup-sync.log'), config('logging.channels.backup-sync.path'));
    }

    public function test_other_signals_are_not_classified_as_expected_interruptions(): void
    {
        $native = Mockery::mock(\Symfony\Component\Process\Process::class);
        $native->shouldReceive('getTermSignal')->andReturn(15);
        $this->fakeWaitFailure(new ProcessSignaledException($native));
        $this->assertNotInstanceOf(UploadBackupInterruptedException::class, $this->assertSafeFailure());
        $this->assertSame('signaled', $this->records[1]['context']['status']);
    }

    public function test_sigkill_mixed_with_another_failure_is_not_suppressed(): void
    {
        $native = Mockery::mock(\Symfony\Component\Process\Process::class);
        $native->shouldReceive('getTermSignal')->andReturn(9);
        $this->fakeWaitFailure(new ProcessSignaledException($native), new RuntimeException('secret-token'));
        $this->assertNotInstanceOf(UploadBackupInterruptedException::class, $this->assertSafeFailure());
        $this->assertSame('sigkill', $this->records[1]['context']['status']);
        $this->assertSame('failed', $this->records[3]['context']['status']);
        $this->assertSame([], $this->successfulDestinations);
    }

    public function test_both_sigkills_are_classified_as_expected_interruption(): void
    {
        $native = Mockery::mock(\Symfony\Component\Process\Process::class);
        $native->shouldReceive('getTermSignal')->andReturn(9);
        $exception = new ProcessSignaledException($native);
        $this->fakeWaitFailure($exception, $exception);
        $this->assertInstanceOf(UploadBackupInterruptedException::class, $this->assertSafeFailure());
        $this->assertSame([], $this->successfulDestinations);
    }

    private function fakeWaitFailure(\Throwable $exception, ?\Throwable $secondException = null): void
    {
        $success = Process::result();
        $pending = Mockery::mock(PendingProcess::class);
        Process::shouldReceive('timeout')->with(600)->twice()->andReturn($pending);
        $pending->shouldReceive('quietly')->twice()->andReturnSelf();
        $attempt = 0;
        $pending->shouldReceive('start')->twice()->andReturnUsing(function (array $command, callable $output) use (&$attempt, $exception, $success, $secondException): InvokedProcess {
            $attempt++;
            $process = Mockery::mock(InvokedProcess::class);
            $process->shouldReceive('id')->once()->andReturn(789);
            $process->shouldReceive('wait')->once()->andReturnUsing(function () use ($output, $exception, $attempt, $success, $secondException) {
                $output('err', '{"level":"error","msg":"secret-token","stats":{"transfers":2,"bytes":');
                $output('err', "99}}\n");
                if ($attempt === 1) {
                    throw $exception;
                }

                if ($secondException !== null) {
                    throw $secondException;
                }

                return $success;
            });

            return $process;
        });
    }

    private function assertSafeFailure(): RuntimeException
    {
        try {
            (new SyncUserUploads)();
            $this->fail('Expected backup failure.');
        } catch (RuntimeException $exception) {
            $this->assertStringNotContainsString('secret', $exception->getMessage());
            $this->assertNull($exception->getPrevious());
            $this->assertStringContainsString('[run_id='.$this->records[0]['context']['run_id'].']', $exception->getMessage());

            return $exception;
        }
    }

    private function assertFailedCallbackDiagnostics(): void
    {
        $this->assertCount(4, $this->records);
        $finished = $this->records[1]['context'];
        $this->assertSame(789, $finished['pid']);
        $this->assertGreaterThanOrEqual(0, $finished['elapsed_seconds']);
        $this->assertSame(99, $finished['diagnostics']['stats']['bytes']);
        $this->assertSame('success', $this->records[3]['context']['status']);
        $this->assertSafeRecords();
    }

    private function assertSafeRecords(): void
    {
        $serialized = json_encode($this->records, JSON_THROW_ON_ERROR);
        $this->assertStringNotContainsString('secret', $serialized);
        $this->assertStringNotContainsString('msg', $serialized);
        $this->assertStringNotContainsString('command', $serialized);
    }
}
