<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak;

/**
 * Simutrans本家 descriptor/intro_dates.h のデフォルト値
 *
 * @see https://github.com/aburch/simutrans/blob/master/src/simutrans/descriptor/intro_dates.h
 */
final class SimutransDefaults
{
    public const int INTRO_YEAR = 1900;

    public const int RETIRE_YEAR = 2999;
}
