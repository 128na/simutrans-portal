<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

final class Kernel extends ConsoleKernel
{
    /**
     * 設置サーバーのcron実行間隔。レンサバだと毎分に設定できないぞい
     */
    private const int CRON_INTERVAL = 2;

    private function getLogPath(): string
    {
        return storage_path(sprintf(
            'logs%sschedule-%s.log',
            DIRECTORY_SEPARATOR,
            now()->toDateString()
        ));
    }

    /**
     * Define the application's command schedule.
     */
    #[\Override]
    protected function schedule(Schedule $schedule): void
    {
        $startAt = now()->toDateTimeString();
        logger()->channel('worker')->info('Kernel::schedule start', ['startAt' => $startAt]);
        $output = $this->getLogPath();

        // 毎日
        $schedule->command('check:deadlink')->dailyAt('10:00')
            ->appendOutputTo($output);
        $schedule->command('backup:clean')->dailyAt('2:00')
            ->appendOutputTo($output);
        $schedule->command('backup:run')->dailyAt('3:00')
            ->appendOutputTo($output);
        $schedule->command('sitemap:generate')->dailyAt('5:00')
            ->appendOutputTo($output);

        // 毎時
        $schedule->command('article:ranking')->hourly()
            ->appendOutputTo($output);
        $schedule->command('article:json')->hourly()
            ->appendOutputTo($output);

        // 毎分 サーバー都合でcron設定としては2分周期
        $schedule->command('article:publish-reservation')->everyMinute()
            ->appendOutputTo($output);
        $schedule->command('queue:work', [
            '--stop-when-empty',
            '--max-time' => (int)(self::CRON_INTERVAL / 2),
        ])->everyMinute()
            ->appendOutputTo($output);

        logger()->channel('worker')->info('Kernel::schedule end', ['startAt' => $startAt]);
    }

    /**
     * Register the commands for the application.
     */
    #[\Override]
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
