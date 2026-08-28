<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Backup;

use App\Actions\Backup\SyncUserUploads;
use App\Console\Commands\Backup\SyncUserUploadsCommand;
use Mockery\MockInterface;
use RuntimeException;
use Tests\Feature\TestCase;

class SyncUserUploadsCommandTest extends TestCase
{
    public function test_command_runs_successfully(): void
    {
        $this->mock(SyncUserUploads::class, function (MockInterface $mock): void {
            $mock->expects('__invoke')->once()->andReturn(5);
        });

        $exitCode = $this->artisan('backup:sync-uploads');

        $exitCode->assertSuccessful();
    }

    public function test_command_fails_when_exception_thrown(): void
    {
        $this->mock(SyncUserUploads::class, function (MockInterface $mock): void {
            $mock->expects('__invoke')
                ->once()
                ->andThrow(new RuntimeException('rclone copy failed'));
        });

        $exitCode = $this->artisan('backup:sync-uploads');

        $exitCode->assertFailed();
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
}
