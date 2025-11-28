<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Services\FileInfo\Extractors\Pak\BuildingTypeConverter;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\WayTypeConverter;

/**
 * Building-specific parser for BUIL nodes
 */
final readonly class BuildingParser implements TypeParserInterface
{
    /**
     * 気候名マッピング
     */
    private const array CLIMATE_NAMES = [
        0 => 'water_climate',
        1 => 'desert_climate',
        2 => 'tropic_climate',
        3 => 'mediterran_climate',
        4 => 'temperate_climate',
        5 => 'tundra_climate',
        6 => 'rocky_climate',
        7 => 'arctic_climate',
    ];

    public function canParse(Node $node): bool
    {
        return $node->type === 'BUIL';
    }

    public function parse(Node $node): array
    {
        $data = $node->data;
        $offset = 0;

        // Read version (may not be present in old versions)
        $firstWord = $this->readUint16($data, $offset);
        $version = ($firstWord & 0x8000) !== 0 ? $firstWord & 0x7FFF : 0;

        if ($version === 0) {
            // Version 0 format (old format)
            return $this->parseVersion0($data);
        }

        // Version 1-11 format
        return $this->parseVersioned($data, $offset, $version);
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion0(string $data): array
    {
        $offset = 0;
        // firstWord is old_btyp, skip it
        $offset += 2;
        $offset += 2; // skip another uint16

        $type = $this->readUint32($data, $offset);
        $level = $this->readUint32($data, $offset);
        $extraData = $this->readUint32($data, $offset);
        $sizeX = $this->readUint16($data, $offset);
        $sizeY = $this->readUint16($data, $offset);
        $layouts = $this->readUint32($data, $offset);
        $offset += 4; // skip climates
        $offset += 4; // skip enables
        $flags = $this->readUint32($data, $offset);

        return $this->buildResult(
            version: 0,
            type: $type,
            level: $level,
            extraData: $extraData,
            sizeX: $sizeX,
            sizeY: $sizeY,
            layouts: $layouts,
            allowedClimates: 0x7F, // all_but_water_climate
            enables: 0x80,
            flags: $flags,
            distributionWeight: 100,
            introDate: 0,
            retireDate: 999912,
            animationTime: 300
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersioned(string $data, int &$offset, int $version): array
    {
        $this->readUint8($data, $offset);
        $type = $this->readUint8($data, $offset);
        $level = $this->readUint16($data, $offset);
        $extraData = $this->readUint32($data, $offset);
        $sizeX = $this->readUint16($data, $offset);
        $sizeY = $this->readUint16($data, $offset);
        $layouts = $this->readUint8($data, $offset);

        // Allowed climates
        $allowedClimates = 0x7F; // Default: all_but_water_climate (all but water)
        if ($version >= 4) {
            $allowedClimates = $this->readUint16($data, $offset) & 0xFF; // ALL_CLIMATES mask
        }

        // Enables
        if ($version >= 3) {
            $enables = $this->readUint8($data, $offset);
        } elseif ($version >= 2) {
            $enables = 0x80;
        } else {
            $enables = 0x80;
        }

        // Flags
        $flags = $this->readUint8($data, $offset);

        // Distribution weight
        $distributionWeight = $version >= 1 ? $this->readUint8($data, $offset) : 100;

        // Intro/retire dates
        if ($version >= 3) {
            $introDate = $this->readUint16($data, $offset);
            $retireDate = $this->readUint16($data, $offset);
        } else {
            $introDate = 0;
            $retireDate = 999912;
        }

        // Animation time
        $animationTime = $version >= 5 ? $this->readUint16($data, $offset) : 300;

        // Capacity, maintenance, price (version 8+)
        $capacity = null;
        $maintenance = null;
        $price = null;
        $allowUnderground = null;

        if ($version >= 8) {
            $capacity = $this->readUint16($data, $offset);

            if ($version >= 11) {
                $maintenance = $this->readInt64($data, $offset);
                $price = $this->readInt64($data, $offset);
            } else {
                // version 8-10
                $maintenance = $this->readInt32($data, $offset);
                $price = $this->readInt32($data, $offset);
            }

            $allowUnderground = $this->readUint8($data, $offset);
        }

        // Preservation year/month (version 10+)
        $preservationYearMonth = null;
        if ($version >= 10) {
            $preservationYearMonth = $this->readUint16($data, $offset);
        }

        return $this->buildResult(
            version: $version,
            type: $type,
            level: $level,
            extraData: $extraData,
            sizeX: $sizeX,
            sizeY: $sizeY,
            layouts: $layouts,
            allowedClimates: $allowedClimates,
            enables: $enables,
            flags: $flags,
            distributionWeight: $distributionWeight,
            introDate: $introDate,
            retireDate: $retireDate,
            animationTime: $animationTime,
            capacity: $capacity,
            maintenance: $maintenance,
            price: $price,
            allowUnderground: $allowUnderground,
            preservationYearMonth: $preservationYearMonth
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function buildResult(
        int $version,
        int $type,
        int $level,
        int $extraData,
        int $sizeX,
        int $sizeY,
        int $layouts,
        int $allowedClimates,
        int $enables,
        int $flags,
        int $distributionWeight,
        int $introDate,
        int $retireDate,
        int $animationTime,
        ?int $capacity = null,
        ?int $maintenance = null,
        ?int $price = null,
        ?int $allowUnderground = null,
        ?int $preservationYearMonth = null
    ): array {
        // Build allowed climates string
        $climateNames = [];
        for ($i = 0; $i < 8; $i++) {
            if (($allowedClimates & (1 << $i)) !== 0) {
                $climateNames[] = self::CLIMATE_NAMES[$i];
            }
        }

        $result = [
            'version' => $version,
            'type' => $type,
            'type_str' => BuildingTypeConverter::getBuildingTypeName($type),
            'level' => $level,
            'size_x' => $sizeX,
            'size_y' => $sizeY,
            'layouts' => $layouts,
            'allowed_climates' => $allowedClimates,
            'allowed_climates_str' => implode(', ', $climateNames),
            'enables' => $enables,
            'flags' => $flags,
            'distribution_weight' => $distributionWeight,
            'intro_date' => $introDate,
            'retire_date' => $retireDate,
            'animation_time' => $animationTime,
        ];

        // Add capacity, maintenance, price if available (version 8+)
        if ($capacity !== null) {
            $result['capacity'] = $capacity;
        }

        if ($maintenance !== null) {
            $result['maintenance'] = $maintenance;
        }

        if ($price !== null) {
            $result['price'] = $price;
        }

        if ($allowUnderground !== null) {
            $result['allow_underground'] = $allowUnderground;
        }

        if ($preservationYearMonth !== null) {
            $result['preservation_year_month'] = $preservationYearMonth;
        }

        // Add type-specific data
        if (BuildingTypeConverter::usesWaytype($type)) {
            // Transport buildings (depot, stop, extension, dock)
            $result['waytype'] = $extraData;
            $result['waytype_str'] = WayTypeConverter::getWayTypeName($extraData);
            $result['enables_str'] = BuildingTypeConverter::getEnablesString($enables);
        } elseif (BuildingTypeConverter::isCityBuilding($type)) {
            // City buildings (residential, commercial, industrial)
            $result['cluster'] = $extraData;
        } elseif ($type === 1 || $type === 2) {
            // Attractions
            $result['min_population'] = $extraData;
        } elseif ($type === 7) {
            // Headquarters
            $result['hq_level'] = $extraData;
        }

        return $result;
    }

    private function readUint8(string $data, int &$offset): int
    {
        $unpacked = unpack('C', substr($data, $offset, 1));
        if ($unpacked === false) {
            throw new \RuntimeException('Failed to unpack uint8');
        }

        $value = $unpacked[1];
        $offset += 1;

        return $value;
    }

    private function readUint16(string $data, int &$offset): int
    {
        $unpacked = unpack('v', substr($data, $offset, 2));
        if ($unpacked === false) {
            throw new \RuntimeException('Failed to unpack uint16');
        }

        $value = $unpacked[1];
        $offset += 2;

        return $value;
    }

    private function readUint32(string $data, int &$offset): int
    {
        $unpacked = unpack('V', substr($data, $offset, 4));
        if ($unpacked === false) {
            throw new \RuntimeException('Failed to unpack uint32');
        }

        $value = $unpacked[1];
        $offset += 4;

        return $value;
    }

    private function readInt32(string $data, int &$offset): int
    {
        $unpacked = unpack('l', substr($data, $offset, 4));
        if ($unpacked === false) {
            throw new \RuntimeException('Failed to unpack int32');
        }

        $value = $unpacked[1];
        $offset += 4;

        return $value;
    }

    private function readInt64(string $data, int &$offset): int
    {
        $unpacked = unpack('q', substr($data, $offset, 8));
        if ($unpacked === false) {
            throw new \RuntimeException('Failed to unpack int64');
        }

        $value = $unpacked[1];
        $offset += 8;

        return $value;
    }
}
