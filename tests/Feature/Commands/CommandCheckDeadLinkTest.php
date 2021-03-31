<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

class CommandCheckDeadLinkTest extends TestCase
{
    public function test()
    {
        $res = $this->artisan('check:deadlink');
        $res->assertExitCode(0);
    }
}
