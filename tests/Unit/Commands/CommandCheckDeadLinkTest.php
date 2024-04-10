<?php

declare(strict_types=1);

namespace Tests\Unit\Commands;

use App\Console\Commands\CommandCheckDeadLink;
use App\Jobs\Article\JobCheckDeadLink;
use Illuminate\Support\Facades\Queue;
use Tests\Unit\TestCase;

final class CommandCheckDeadLinkTest extends TestCase
{
    private function getSUT(): CommandCheckDeadLink
    {
        return app(CommandCheckDeadLink::class);
    }

    public function test(): void
    {
        Queue::fake();
        $result = $this->getSUT()->handle();
        $this->assertSame(0, $result);
        Queue::assertPushed(JobCheckDeadLink::class);
    }
}
