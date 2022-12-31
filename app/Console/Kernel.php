<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<string>
     */
    protected $commands = [];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 毎分
        $schedule->command('article:publish-reservation')->everyMinute()
            ->withoutOverlapping()
            ->onOneServer();

        // 毎時
        $schedule->command('ranking:update')->hourly()
            ->withoutOverlapping()
            ->onOneServer();

        // 毎日
        $schedule->command('check:deadlink')->dailyAt('10:00')
            ->withoutOverlapping()
            ->onOneServer();
        $schedule->command('backup:clean')->dailyAt('2:00')
            ->withoutOverlapping()
            ->onOneServer();
        $schedule->command('backup:run')->dailyAt('3:00')
            ->withoutOverlapping()
            ->onOneServer();
        $schedule->command('compress:image')->dailyAt('4:00')
            ->withoutOverlapping()
            ->onOneServer();
        $schedule->command('delete:tags')->dailyAt('4:00')
            ->withoutOverlapping()
            ->onOneServer();
        $schedule->command('tweet_log:update_by_timeline -w')->dailyAt('5:00')
            ->withoutOverlapping()
            ->onOneServer();
        $schedule->command('tweet_log:update_by_timeline')->dailyAt('5:30')
            ->withoutOverlapping()
            ->onOneServer();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
