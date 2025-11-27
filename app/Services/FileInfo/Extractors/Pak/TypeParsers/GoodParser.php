<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\ObjectTypeConverter;
use RuntimeException;

/**
 * Parser for goods/freight (GOOD) nodes
 *
 * Goods define cargo types that can be transported.
 * Supported versions: 0-4
 */
final readonly class GoodParser implements TypeParserInterface
{
    // Good categories (from goods_desc.h / goods_manager.cc)
    private const CATEGORY_NAMES = [
        0 => 'special_freight',
        1 => 'piece_goods',
        2 => 'bulk_goods',
        3 => 'long_goods',
        4 => 'liquid_goods',
        5 => 'cooled_goods',
        6 => 'passengers',
        7 => 'mail',
        8 => 'none',
    ];

    public function canParse(Node $node): bool
    {
        $objectType = ObjectTypeConverter::toString($node->type);

        return $objectType === 'good';
    }

    /**
     * @return array<string, mixed>
     */
    public function parse(Node $node): array
    {
        $binaryData = $node->data;
        $offset = 0;

        // Read first uint16
        $firstUint16Data = unpack('v', substr($binaryData, $offset, 2));
        if ($firstUint16Data === false) {
            throw new RuntimeException('Failed to read goods version/value');
        }
        $firstUint16 = $firstUint16Data[1];

        // Check if high bit is set (versioned format)
        if (($firstUint16 & 0x8000) !== 0) {
            $version = $firstUint16 & 0x7FFF; // Mask out high bit
            $offset += 2;

            return match ($version) {
                1 => $this->parseVersion1($binaryData, $offset),
                2 => $this->parseVersion2($binaryData, $offset),
                3 => $this->parseVersion3($binaryData, $offset),
                4 => $this->parseVersion4($binaryData, $offset),
                default => throw new RuntimeException("Unsupported goods version: {$version}"),
            };
        }

        // Version 0 (legacy format): firstUint16 is actually base_value
        return $this->parseVersion0($binaryData, $offset, $firstUint16);
    }

    /**
     * Parse version 0 (legacy - no version stamp)
     *
     * @return array<string, mixed>
     */
    private function parseVersion0(string $binaryData, int $offset, int $baseValue): array
    {
        $result = [
            'version' => 0,
            'base_value' => $baseValue,
        ];

        // catg (uint16)
        $catgData = unpack('v', substr($binaryData, $offset, 2));
        if ($catgData === false) {
            throw new RuntimeException('Failed to read catg');
        }
        $result['catg'] = $catgData[1] & 0xFF; // Cast to uint8

        // Set defaults for missing fields
        $result['speed_bonus'] = 0;
        $result['weight_per_unit'] = 100;
        $result['color'] = 255;

        return $this->buildResult($result);
    }

    /**
     * Parse version 1
     *
     * @return array<string, mixed>
     */
    private function parseVersion1(string $binaryData, int $offset): array
    {
        $result = ['version' => 1];

        // base_value (uint16)
        $baseValueData = unpack('v', substr($binaryData, $offset, 2));
        if ($baseValueData === false) {
            throw new RuntimeException('Failed to read base_value');
        }
        $result['base_value'] = $baseValueData[1];
        $offset += 2;

        // catg (uint16, cast to uint8)
        $catgData = unpack('v', substr($binaryData, $offset, 2));
        if ($catgData === false) {
            throw new RuntimeException('Failed to read catg');
        }
        $result['catg'] = $catgData[1] & 0xFF;
        $offset += 2;

        // speed_bonus (uint16) - NEW in version 1
        $speedBonusData = unpack('v', substr($binaryData, $offset, 2));
        if ($speedBonusData === false) {
            throw new RuntimeException('Failed to read speed_bonus');
        }
        $result['speed_bonus'] = $speedBonusData[1];

        // Set defaults
        $result['weight_per_unit'] = 100;
        $result['color'] = 255;

        return $this->buildResult($result);
    }

    /**
     * Parse version 2
     *
     * @return array<string, mixed>
     */
    private function parseVersion2(string $binaryData, int $offset): array
    {
        $result = ['version' => 2];

        // base_value (uint16)
        $baseValueData = unpack('v', substr($binaryData, $offset, 2));
        if ($baseValueData === false) {
            throw new RuntimeException('Failed to read base_value');
        }
        $result['base_value'] = $baseValueData[1];
        $offset += 2;

        // catg (uint16, cast to uint8)
        $catgData = unpack('v', substr($binaryData, $offset, 2));
        if ($catgData === false) {
            throw new RuntimeException('Failed to read catg');
        }
        $result['catg'] = $catgData[1] & 0xFF;
        $offset += 2;

        // speed_bonus (uint16)
        $speedBonusData = unpack('v', substr($binaryData, $offset, 2));
        if ($speedBonusData === false) {
            throw new RuntimeException('Failed to read speed_bonus');
        }
        $result['speed_bonus'] = $speedBonusData[1];
        $offset += 2;

        // weight_per_unit (uint16) - NEW in version 2
        $weightData = unpack('v', substr($binaryData, $offset, 2));
        if ($weightData === false) {
            throw new RuntimeException('Failed to read weight_per_unit');
        }
        $result['weight_per_unit'] = $weightData[1];

        // Set defaults
        $result['color'] = 255;

        return $this->buildResult($result);
    }

    /**
     * Parse version 3
     *
     * @return array<string, mixed>
     */
    private function parseVersion3(string $binaryData, int $offset): array
    {
        $result = ['version' => 3];

        // base_value (uint16)
        $baseValueData = unpack('v', substr($binaryData, $offset, 2));
        if ($baseValueData === false) {
            throw new RuntimeException('Failed to read base_value');
        }
        $result['base_value'] = $baseValueData[1];
        $offset += 2;

        // catg (uint8) - CHANGED in version 3
        $catgData = unpack('C', substr($binaryData, $offset, 1));
        if ($catgData === false) {
            throw new RuntimeException('Failed to read catg');
        }
        $result['catg'] = $catgData[1];
        $offset += 1;

        // speed_bonus (uint16)
        $speedBonusData = unpack('v', substr($binaryData, $offset, 2));
        if ($speedBonusData === false) {
            throw new RuntimeException('Failed to read speed_bonus');
        }
        $result['speed_bonus'] = $speedBonusData[1];
        $offset += 2;

        // weight_per_unit (uint16)
        $weightData = unpack('v', substr($binaryData, $offset, 2));
        if ($weightData === false) {
            throw new RuntimeException('Failed to read weight_per_unit');
        }
        $result['weight_per_unit'] = $weightData[1];
        $offset += 2;

        // color (uint8) - NEW in version 3
        $colorData = unpack('C', substr($binaryData, $offset, 1));
        if ($colorData === false) {
            throw new RuntimeException('Failed to read color');
        }
        $result['color'] = $colorData[1];

        return $this->buildResult($result);
    }

    /**
     * Parse version 4 (64-bit base_value)
     *
     * @return array<string, mixed>
     */
    private function parseVersion4(string $binaryData, int $offset): array
    {
        $result = ['version' => 4];

        // base_value (sint64) - CHANGED in version 4
        $result['base_value'] = $this->readInt64($binaryData, $offset);
        $offset += 8;

        // catg (uint8)
        $catgData = unpack('C', substr($binaryData, $offset, 1));
        if ($catgData === false) {
            throw new RuntimeException('Failed to read catg');
        }
        $result['catg'] = $catgData[1];
        $offset += 1;

        // speed_bonus (uint16)
        $speedBonusData = unpack('v', substr($binaryData, $offset, 2));
        if ($speedBonusData === false) {
            throw new RuntimeException('Failed to read speed_bonus');
        }
        $result['speed_bonus'] = $speedBonusData[1];
        $offset += 2;

        // weight_per_unit (uint16)
        $weightData = unpack('v', substr($binaryData, $offset, 2));
        if ($weightData === false) {
            throw new RuntimeException('Failed to read weight_per_unit');
        }
        $result['weight_per_unit'] = $weightData[1];
        $offset += 2;

        // color (uint8)
        $colorData = unpack('C', substr($binaryData, $offset, 1));
        if ($colorData === false) {
            throw new RuntimeException('Failed to read color');
        }
        $result['color'] = $colorData[1];

        return $this->buildResult($result);
    }

    /**
     * Read signed 64-bit integer (little-endian)
     */
    private function readInt64(string $binaryData, int $offset): int
    {
        $data = unpack('P', substr($binaryData, $offset, 8));
        if ($data === false) {
            throw new RuntimeException('Failed to read int64');
        }

        return $data[1];
    }

    /**
     * Build final result with human-readable category name
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function buildResult(array $data): array
    {
        // Add category name string
        $catg = $data['catg'] ?? 8; // Default to 'none'
        $data['catg_name'] = self::CATEGORY_NAMES[is_int($catg) && isset(self::CATEGORY_NAMES[$catg]) ? $catg : 8];

        return $data;
    }
}
