<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

use Carbon\CarbonImmutable;
use Illuminate\Console\Scheduling\Schedule;
use Tests\Unit\TestCase;

class XDigestScheduleTest extends TestCase
{
    public function test_digest_runs_only_on_monday_and_thursday_at_2130_in_japan(): void
    {
        config(['app.timezone' => 'UTC']);
        $events = collect($this->app->make(Schedule::class)->events())
            ->filter(fn ($event): bool => str_contains($event->command ?? '', 'sns:x-daily-digest'))
            ->values();

        $this->assertCount(1, $events);
        $event = $events->sole();
        $this->assertTrue($event->withoutOverlapping);

        for ($day = 0; $day < 7; $day++) {
            foreach (['12:00', '21:29', '21:30', '21:31'] as $time) {
                $date = CarbonImmutable::parse('2026-10-12 '.$time, 'Asia/Tokyo')->addDays($day);
                $this->travelTo($date->setTimezone('UTC'));

                $this->assertSame(
                    in_array($day, [0, 3], true) && $time === '21:30',
                    $event->isDue($this->app),
                    $date->toIso8601String(),
                );
            }
        }
    }
}
