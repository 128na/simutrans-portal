<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;

class CommandDeleteUnreatedTagsTest extends TestCase
{
    public function test()
    {
        $res = $this->artisan('delete:tags');
        $res->assertExitCode(0);
    }
}
