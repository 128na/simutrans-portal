<?php

declare(strict_types=1);

namespace Tests\Feature\Commands;

use Tests\TestCase;

class CommandCompressImageTest extends TestCase
{
    public function test()
    {
        $res = $this->artisan('compress:image');
        $res->assertExitCode(0);
    }
}
