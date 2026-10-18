<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Backup;

use App\Actions\Backup\SyncUploadsDigestTracker;
use App\Actions\Backup\SyncUserUploads;
use App\Actions\Backup\UploadBackupHealth;
use App\Console\Commands\Backup\SyncUserUploadsCommand;
use App\Exceptions\UploadBackupInterruptedException;
use Illuminate\Process\FakeProcessDescription;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Process;
use Mockery;
use Mockery\MockInterface;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Tests\Feature\TestCase;

class SyncUserUploadsCommandTest extends TestCase
{
    public function test_command_runs_successfully(): void
    {
        $this->mock(SyncUserUploads::class, function (MockInterface $mock): void {
            $mock->expects('__invoke')->once()->andReturn(5);
        });

        $this->artisan('backup:sync-uploads')->assertSuccessful();
    }

    public function test_command_fails_when_exception_thrown(): void
    {
        Exceptions::fake();
        $this->mock(SyncUserUploads::class, function (MockInterface $mock): void {
            $mock->expects('__invoke')
                ->once()
                ->andThrow(new RuntimeException('rclone copy failed'));
        });

        $this->artisan('backup:sync-uploads')->assertFailed();
        Exceptions::assertReported(RuntimeException::class);
    }

    public function test_sigkill_records_digest_error_and_failure_without_reporting(): void
    {
        Exceptions::fake();
        $this->mock(SyncUserUploads::class)->expects('__invoke')->once()->andThrow(new UploadBackupInterruptedException('Backup interrupted.'));
        $this->mock(SyncUploadsDigestTracker::class)->expects('recordError')->once();

        $this->artisan('backup:sync-uploads')->expectsOutput('Backup interrupted.')->assertFailed();

        Exceptions::assertNothingReported();
    }

    public function test_real_action_sigkill_is_not_reported_but_daily_error_and_failure_are_preserved(): void
    {
        $this->runActualActionWithSigkill(false);
    }

    public function test_real_action_sigkill_mixed_with_exit_error_is_reported(): void
    {
        $this->runActualActionWithSigkill(true);
    }

    public function test_command_signature_is_correct(): void
    {
        $command = $this->app->make(SyncUserUploadsCommand::class);

        $this->assertEquals('backup:sync-uploads', $command->getName());
    }

    public function test_command_description_exists(): void
    {
        $command = $this->app->make(SyncUserUploadsCommand::class);

        $this->assertNotEmpty($command->getDescription());
    }

    private function runActualActionWithSigkill(bool $otherFailure): void
    {
        Exceptions::fake();
        Cache::flush();
        $this->travelTo(now()->setSecond(0));
        config(['rclone.uploads_remote' => 'remote:path', 'rclone.uploads_local_backup' => 'local-path']);
        $this->mock(UploadBackupHealth::class)->shouldReceive('recordSuccess')->zeroOrMoreTimes();
        $native = Mockery::mock(\Symfony\Component\Process\Process::class);
        $native->shouldReceive('getTermSignal')->andReturn(9);
        $attempts = 0;
        Process::fake(['*' => function () use (&$attempts, $native, $otherFailure): FakeProcessDescription {
            if (++$attempts === 1) {
                throw new ProcessSignaledException($native);
            }

            return Process::describe()->exitCode($otherFailure ? 1 : 0);
        }]);

        $this->artisan('backup:sync-uploads')->assertFailed();

        $this->assertSame(2, $attempts);
        $this->assertSame(1, app(SyncUploadsDigestTracker::class)->errorsOn(now()->toDateString()));
        if ($otherFailure) {
            Exceptions::assertReported(RuntimeException::class);
            Exceptions::assertNotReported(UploadBackupInterruptedException::class);
        } else {
            Exceptions::assertNothingReported();
        }
    }
}
