<?php

declare(strict_types=1);

namespace Tests\Feature\Commands;

use Illuminate\Support\Sleep;
use Tests\Feature\TestCase;

final class CommandCheckDeadLinkTest extends TestCase
{
    public function test(): void
    {
        Sleep::fake();
        $res = $this->artisan('check:deadlink');
        $res->assertExitCode(0);
    }
}
