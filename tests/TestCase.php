<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Sleep;

abstract class TestCase extends BaseTestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        Sleep::fake();
    }
}
