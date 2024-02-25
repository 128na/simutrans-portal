<?php

declare(strict_types=1);

namespace App\Channels;

abstract class BaseChannel
{
    abstract public static function featureEnabled(): bool;
}
