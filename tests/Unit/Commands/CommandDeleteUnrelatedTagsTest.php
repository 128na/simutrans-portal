<?php

declare(strict_types=1);

namespace Tests\Unit\Commands;

use App\Console\Commands\CommandDeleteUnrelatedTags;
use App\Jobs\Article\JobDeleteUnrelatedTags;
use Illuminate\Support\Facades\Queue;
use Tests\Unit\TestCase;

final class CommandDeleteUnrelatedTagsTest extends TestCase
{
    private function getSUT(): CommandDeleteUnrelatedTags
    {
        return app(CommandDeleteUnrelatedTags::class);
    }

    public function test(): void
    {
        Queue::fake();
        $result = $this->getSUT()->handle();
        $this->assertSame(0, $result);
        Queue::assertPushed(JobDeleteUnrelatedTags::class);
    }
}
