<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // 抽象クラスなので、具体的な実装は Unit\TestCase または Feature\TestCase で行う
}
