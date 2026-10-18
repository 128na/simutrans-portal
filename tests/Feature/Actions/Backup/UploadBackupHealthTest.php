<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Backup;

use App\Actions\Backup\SyncUserUploads;
use App\Actions\Backup\UploadBackupHealth;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use RuntimeException;
use Tests\Feature\TestCase;

class UploadBackupHealthTest extends TestCase
{
    public function test_monitor_grants_initial_grace_and_alerts_without_any_sync_runs(): void
    {
        Log::spy();
        $this->artisan('backup:monitor-uploads')->assertSuccessful();
        $this->travel(359)->minutes();
        $this->artisan('backup:monitor-uploads')->assertSuccessful();
        Log::shouldNotHaveReceived('error');
        $this->travel(1)->minutes();
        $this->artisan('backup:monitor-uploads')->assertFailed();
        Log::shouldHaveReceived('error')->twice();
    }

    public function test_destinations_are_independent_and_alerts_are_rate_limited(): void
    {
        Log::spy();
        $health = new UploadBackupHealth;
        $health->check();
        $this->travel(6)->hours();
        $health->recordSuccess('local_backup');
        $this->assertSame(['dropbox'], $health->check());
        $this->assertSame(['dropbox'], $health->check());
        Log::shouldHaveReceived('error')->once()->withArgs(fn (string $message, array $context): bool => $context['destination'] === 'dropbox');
        $this->travel(6)->hours();
        $health->recordSuccess('local_backup');
        $this->assertSame(['dropbox'], $health->check());
        Log::shouldHaveReceived('error')->twice();
    }

    public function test_recovery_resets_alert_state_and_uses_last_complete_time(): void
    {
        Log::spy();
        $health = new UploadBackupHealth;
        $health->check();
        $this->travel(6)->hours();
        $health->check();
        $health->recordSuccess('dropbox');
        $health->recordSuccess('local_backup');
        $this->assertSame([], $health->check());
        $this->travel(359)->minutes();
        $this->assertSame([], $health->check());
        $this->travel(1)->minutes();
        $this->assertSame(['dropbox', 'local_backup'], $health->check());
        Log::shouldHaveReceived('error')->times(4);
    }

    public function test_monitor_is_scheduled_independently_every_hour(): void
    {
        $events = collect(app(Schedule::class)->events())->filter(fn ($event): bool => str_contains($event->command ?? '', 'backup:monitor-uploads'));
        $this->assertCount(1, $events);
        $this->assertSame('0 * * * *', $events->first()->expression);
    }

    public function test_duration_limit_does_not_mask_staleness_but_complete_sync_recovers(): void
    {
        Log::spy();
        $health = app(UploadBackupHealth::class);
        $health->check();
        $this->travel(6)->hours();

        Process::fake(['*' => Process::result(exitCode: 10)]);
        app(SyncUserUploads::class)();
        $this->assertSame(['dropbox', 'local_backup'], $health->check());

        Process::fake();
        app(SyncUserUploads::class)();
        $this->assertSame([], $health->check());
    }

    public function test_failed_notification_does_not_suppress_the_next_attempt(): void
    {
        $health = new UploadBackupHealth;
        $health->check();
        $this->travel(6)->hours();
        $health->recordSuccess('local_backup');
        $attempts = 0;
        Log::shouldReceive('error')->twice()->andReturnUsing(function () use (&$attempts): void {
            if (++$attempts === 1) {
                throw new RuntimeException('notification unavailable');
            }
        });

        try {
            $health->check();
            $this->fail('Expected notification failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('notification unavailable', $exception->getMessage());
        }

        $this->assertSame(['dropbox'], $health->check());
    }
}
