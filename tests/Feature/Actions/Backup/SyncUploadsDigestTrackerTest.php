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

        $digest->recordTransferred(3);
        $digest->recordTransferred(5);

        $this->assertSame(8, $digest->transferredToday());
        $this->assertSame(0, $digest->errorsToday());
    }

    public function test_accumulates_error_count_across_multiple_calls(): void
    {
        $digest = new SyncUploadsDigestTracker;

        $digest->recordError();
        $digest->recordError();

        $this->assertSame(2, $digest->errorsToday());
        $this->assertSame(0, $digest->transferredToday());
    }

    public function test_recording_zero_transferred_does_not_create_a_counter(): void
    {
        $digest = new SyncUploadsDigestTracker;

        $digest->recordTransferred(0);

        $this->assertSame(0, $digest->transferredToday());
    }
}
