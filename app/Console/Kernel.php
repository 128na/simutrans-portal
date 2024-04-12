<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

final class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    #[\Override]
    protected function schedule(Schedule $schedule): void
    {
        // 毎分 サーバー都合でcron設定としては5分周期
        $schedule->command('queue:cron')->everyMinute()
            ->runInBackground()
            ->withoutOverlapping()
            ->onOneServer();

        $schedule->command('article:publish-reservation')->everyMinute()
            ->runInBackground()
            ->withoutOverlapping()
            ->onOneServer();

        // 毎時
        $schedule->command('article:ranking')->hourly()
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
        $schedule->command('sitemap:generate')->dailyAt('5:00')
            ->runInBackground()
            ->withoutOverlapping()
            ->onOneServer();
    }

    /**
     * Register the commands for the application.
     */
    #[\Override]
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
