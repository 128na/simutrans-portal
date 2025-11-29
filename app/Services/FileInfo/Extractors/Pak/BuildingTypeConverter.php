<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak;

/**
 * Building type converter
 */
final class BuildingTypeConverter
{
    /**
     * Check if building type uses waytype
     */
    public static function usesWaytype(int $type): bool
    {
        return in_array($type, [11, 33, 34, 35, 36], true);
    }

    /**
     * Check if building type is city building
     */
    public static function isCityBuilding(int $type): bool
    {
        return in_array($type, [37, 38, 39], true);
    }
}
