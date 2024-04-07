<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        if (! defined('T')) {
            define('T', true);
        }
        if (! defined('F')) {
            define('F', false);
        }
        parent::setUp();
    }
}
