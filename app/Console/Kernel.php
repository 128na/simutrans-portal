<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // 毎分 サーバー都合でcron設定としては5分周期
        $schedule->command('queue:work', ['--max-time' => 295])->everyMinute()
            ->runInBackground()
            ->withoutOverlapping()
            ->onOneServer();
        $schedule->command('article:publish-reservation')->everyMinute()
            ->runInBackground()
            ->withoutOverlapping()
            ->onOneServer();

        // 毎時
        $schedule->command('ranking:update')->hourly()
            ->withoutOverlapping()
            ->onOneServer();

        // 毎日
        $schedule->command('check:deadlink')->dailyAt('10:00')
            ->runInBackground()
            ->withoutOverlapping()
            ->onOneServer();
        $schedule->command('backup:clean')->dailyAt('2:00')
            ->runInBackground()
            ->withoutOverlapping()
            ->onOneServer();
        $schedule->command('backup:run')->dailyAt('3:00')
            ->runInBackground()
            ->withoutOverlapping()
            ->onOneServer();
        $schedule->command('delete:tags')->dailyAt('4:00')
            ->runInBackground()
            ->withoutOverlapping()
            ->onOneServer();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
