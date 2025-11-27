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
            0 => 'road',
            1 => 'track',
            2 => 'water',
            3 => 'air',
            4 => 'monorail',
            5 => 'maglev',
            6 => 'tram',
            7 => 'narrowgauge',
            8 => 'powerline',
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
