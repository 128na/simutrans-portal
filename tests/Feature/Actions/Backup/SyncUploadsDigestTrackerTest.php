<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Backup;

use App\Actions\Backup\SyncUploadsDigestTracker;
use Tests\Feature\TestCase;

class SyncUploadsDigestTrackerTest extends TestCase
{
    public function test_accumulates_transferred_count_across_multiple_calls(): void
    {
        $digest = new SyncUploadsDigestTracker;
        $today = now()->toDateString();

        $digest->recordTransferred(3);
        $digest->recordTransferred(5);

        $this->assertSame(8, $digest->transferredOn($today));
        $this->assertSame(0, $digest->errorsOn($today));
    }

    public function test_accumulates_error_count_across_multiple_calls(): void
    {
        $digest = new SyncUploadsDigestTracker;
        $today = now()->toDateString();

        $digest->recordError();
        $digest->recordError();

        $this->assertSame(2, $digest->errorsOn($today));
        $this->assertSame(0, $digest->transferredOn($today));
    }

    public function test_recording_zero_transferred_does_not_create_a_counter(): void
    {
        $digest = new SyncUploadsDigestTracker;
        $today = now()->toDateString();

        $digest->recordTransferred(0);

        $this->assertSame(0, $digest->transferredOn($today));
    }

    public function test_counters_are_isolated_per_date(): void
    {
        $digest = new SyncUploadsDigestTracker;

        $digest->recordTransferred(7);

        $this->assertSame(0, $digest->transferredOn(now()->subDay()->toDateString()));
        $this->assertSame(0, $digest->transferredOn(now()->addDay()->toDateString()));
    }
}
