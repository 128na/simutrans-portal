<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PDOException;
use Tests\Unit\TestCase;

class SchedulerFileCacheTest extends TestCase
{
    public function test_scheduler_can_pause_and_prevent_overlap_during_a_database_outage(): void
    {
        $path = storage_path('framework/testing/cache-'.Str::uuid());
        config([
            'cache.default' => 'file',
            'cache.stores.file.path' => $path,
            'database.connections.unavailable' => ['driver' => 'unavailable'],
            'database.default' => 'unavailable',
        ]);
        DB::extend('unavailable', fn () => throw new PDOException('Connection refused'));

        $schedule = $this->app->make(Schedule::class);
        foreach ($schedule->events() as $event) {
            $event->skip(fn (): bool => true);
        }

        $runs = 0;
        $event = $schedule->call(function () use (&$runs): void {
            $runs++;
        })->name('file-cache-probe')->withoutOverlapping();

        try {
            $this->artisan('schedule:pause')->assertSuccessful();
            $this->artisan('schedule:run')->assertSuccessful();
            $this->assertSame(0, $runs);

            $this->artisan('schedule:resume')->assertSuccessful();
            $this->assertTrue($event->mutex->create($event));
            $this->assertFalse($event->mutex->create($event));
            $this->artisan('schedule:run')->assertSuccessful();
            $this->assertSame(0, $runs);

            $event->mutex->forget($event);
            $this->artisan('schedule:run')->assertSuccessful();
            $this->assertSame(1, $runs);
            $this->assertFalse($event->mutex->exists($event));
        } finally {
            File::deleteDirectory($path);
        }
    }
}
