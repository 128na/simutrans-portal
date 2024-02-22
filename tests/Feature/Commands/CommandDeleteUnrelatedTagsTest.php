<?php

declare(strict_types=1);

namespace Tests\Feature\Commands;

use Tests\TestCase;

class CommandDeleteUnrelatedTagsTest extends TestCase
{
    public function test()
    {
        $res = $this->artisan('delete:tags');
        $res->assertExitCode(0);
    }
}
