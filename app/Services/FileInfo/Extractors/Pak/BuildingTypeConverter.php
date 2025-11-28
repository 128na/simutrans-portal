<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak;

/**
 * Building type converter
 */
final class BuildingTypeConverter
{
    /**
     * Convert building type ID to human-readable string
     */
    public static function getBuildingTypeName(int $type): string
    {
        return match ($type) {
            0 => 'Unknown',
            1 => 'City Attraction',
            2 => 'Land Attraction',
            3 => 'Monument',
            4 => 'Factory',
            5 => 'Town Hall',
            6 => 'Others',
            7 => 'Headquarters',
            11 => 'Dock',
            33 => 'Depot',
            34 => 'Stop',
            35 => 'Stop Extension',
            36 => 'Flat Dock',
            37 => 'Residential',
            38 => 'Commercial',
            39 => 'Industrial',
            default => sprintf('Unknown Type (%s)', $type),
        };
    }

    /**
     * Convert enables bitfield to human-readable string
     */
    public static function getEnablesString(int $enables): string
    {
        $features = [];

        if (($enables & 0x01) !== 0) {
            $features[] = 'Passengers';
        }

        if (($enables & 0x02) !== 0) {
            $features[] = 'Mail';
        }

        if (($enables & 0x04) !== 0) {
            $features[] = 'Goods';
        }

        return $features === [] ? 'None' : implode(', ', $features);
    }

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
