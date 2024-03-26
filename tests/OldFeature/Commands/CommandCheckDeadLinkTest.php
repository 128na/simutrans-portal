<?php

declare(strict_types=1);

namespace Tests\OldFeature\Commands;

use Tests\TestCase;

class CommandCheckDeadLinkTest extends TestCase
{
    public function test(): void
    {
        $res = $this->artisan('check:deadlink');
        $res->assertExitCode(0);
    }
}
