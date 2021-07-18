<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('passport:purge')->hourly();

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
