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
            default => "Unknown Type ($type)",
        };
    }

    /**
     * Convert waytype ID to human-readable string
     */
    public static function getWaytypeName(int $waytype): string
    {
        return match ($waytype) {
            1 => 'Track',
            3 => 'Road',
            4 => 'Water',
            5 => 'Monorail',
            7 => 'Tram',
            16 => 'Air',
            default => "Unknown Waytype ($waytype)",
        };
    }

    /**
     * Convert enables bitfield to human-readable string
     */
    public static function getEnablesString(int $enables): string
    {
        $features = [];

        if ($enables & 0x01) {
            $features[] = 'Passengers';
        }
        if ($enables & 0x02) {
            $features[] = 'Mail';
        }
        if ($enables & 0x04) {
            $features[] = 'Goods';
        }

        return empty($features) ? 'None' : implode(', ', $features);
    }

    /**
     * Check if building type uses waytype
     */
    public static function usesWaytype(int $type): bool
    {
        return in_array($type, [11, 33, 34, 35, 36], true);
    }

    /**
     * Check if building type is transport-related
     */
    public static function isTransportBuilding(int $type): bool
    {
        return $type > 7 && $type <= 36;
    }

    /**
     * Check if building type is city building
     */
    public static function isCityBuilding(int $type): bool
    {
        return in_array($type, [37, 38, 39], true);
    }
}
