<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Backup;

use App\Actions\Backup\SyncUploadsDigestTracker;
use App\Console\Commands\Backup\ReportSyncUploadsDigestCommand;
use Illuminate\Support\Facades\Log;
use Mockery\MockInterface;
use Tests\Feature\TestCase;

class ReportSyncUploadsDigestCommandTest extends TestCase
{
    public function test_does_not_notify_when_nothing_happened_yesterday(): void
    {
        $this->mock(SyncUploadsDigestTracker::class, function (MockInterface $mock): void {
            $mock->expects('transferredOn')->andReturn(0);
            $mock->expects('errorsOn')->andReturn(0);
        });

        Log::shouldReceive('channel')->never();

        $exitCode = $this->artisan('backup:report-sync-uploads-digest');

        $exitCode->assertSuccessful();
    }

    public function test_notifies_when_files_were_transferred(): void
    {
        $this->mock(SyncUploadsDigestTracker::class, function (MockInterface $mock): void {
            $mock->expects('transferredOn')->andReturn(12);
            $mock->expects('errorsOn')->andReturn(0);
        });

        $expectedDate = now()->subDay()->toDateString();

        Log::shouldReceive('channel')->once()->with('discord_backup')->andReturnSelf();
        Log::shouldReceive('info')->once()->with("ユーザーアップロード同期({$expectedDate}分): 12件転送, 0エラー");

        $exitCode = $this->artisan('backup:report-sync-uploads-digest');

        $exitCode->assertSuccessful();
    }

    public function test_notifies_when_there_were_errors_even_with_zero_transfers(): void
    {
        $this->mock(SyncUploadsDigestTracker::class, function (MockInterface $mock): void {
            $mock->expects('transferredOn')->andReturn(0);
            $mock->expects('errorsOn')->andReturn(3);
        });

        $expectedDate = now()->subDay()->toDateString();

        Log::shouldReceive('channel')->once()->with('discord_backup')->andReturnSelf();
        Log::shouldReceive('info')->once()->with("ユーザーアップロード同期({$expectedDate}分): 0件転送, 3エラー");

        $exitCode = $this->artisan('backup:report-sync-uploads-digest');

        $exitCode->assertSuccessful();
    }

    public function test_reads_yesterdays_date_not_today(): void
    {
        $expectedDate = now()->subDay()->toDateString();

        $this->mock(SyncUploadsDigestTracker::class, function (MockInterface $mock) use ($expectedDate): void {
            $mock->expects('transferredOn')->with($expectedDate)->andReturn(0);
            $mock->expects('errorsOn')->with($expectedDate)->andReturn(0);
        });

        $exitCode = $this->artisan('backup:report-sync-uploads-digest');

        $exitCode->assertSuccessful();
    }

    public function test_command_signature_is_correct(): void
    {
        $command = $this->app->make(ReportSyncUploadsDigestCommand::class);

        $this->assertEquals('backup:report-sync-uploads-digest', $command->getName());
    }
}
