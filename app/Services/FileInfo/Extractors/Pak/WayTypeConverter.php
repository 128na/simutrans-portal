<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak;

/**
 * Convert way type IDs to human-readable strings
 */
final readonly class WayTypeConverter
{
    /**
     * Get way type name from wtyp value
     */
    public static function getWayTypeName(int $wtyp): string
    {
        return match ($wtyp) {
            0 => 'ignore',
            1 => 'road',
            2 => 'track',
            3 => 'water',
            4 => 'overheadlines',
            5 => 'monorail',
            6 => 'maglev',
            7 => 'tram',
            8 => 'narrowgauge',
            16 => 'air',
            128 => 'powerline',
            255 => 'any',
            default => sprintf('unknown(%s)', $wtyp),
        };
    }

    /**
     * Get system type name from styp value
     */
    public static function getSystemTypeName(int $styp): string
    {
        return match ($styp) {
            0 => 'flat',
            1 => 'elevated',
            2 => 'tram',
            3 => 'embankment',
            4 => 'tunnel',
            5 => 'runway',
            default => sprintf('unknown(%s)', $styp),
        };
    }
}
