<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\ObjectTypeConverter;
use App\Services\FileInfo\Extractors\Pak\WayTypeConverter;
use RuntimeException;

/**
 * Parser for way-object (WAYOBJ) nodes
 *
 * Way objects are infrastructure placed on ways, primarily overhead lines (catenary).
 * Supported versions: 1-2
 *
 * @see simutrans/descriptor/reader/way_obj_reader.cc
 */
final readonly class WayObjectParser implements TypeParserInterface
{
    public function canParse(Node $node): bool
    {
        $objectType = ObjectTypeConverter::toString($node->type);

        return $objectType === 'wayobj';
    }

    /**
     * @return array<string, mixed>
     */
    public function parse(Node $node): array
    {
        $binaryData = $node->data;
        $offset = 0;

        // Read version (uint16 with high bit as version marker)
        $versionData = unpack('v', substr($binaryData, $offset, 2));
        if ($versionData === false) {
            throw new RuntimeException('Failed to read way-object version');
        }

        $versionRaw = $versionData[1];
        $version = $versionRaw & 0x7FFF; // Mask out high bit

        $offset += 2;

        if ($version === 1) {
            return $this->parseVersion1($binaryData, $offset);
        }

        if ($version === 2) {
            return $this->parseVersion2($binaryData, $offset);
        }

        throw new RuntimeException('Unsupported way-object version: '.$version);
    }

    /**
     * Parse version 1 (uint32 for price/maintenance)
     *
     * @return array<string, mixed>
     */
    private function parseVersion1(string $binaryData, int $offset): array
    {
        $result = ['version' => 1];

        // price (uint32)
        $priceData = unpack('V', substr($binaryData, $offset, 4));
        if ($priceData === false) {
            throw new RuntimeException('Failed to read price');
        }

        $result['price'] = $priceData[1];
        $offset += 4;

        // maintenance (uint32)
        $maintenanceData = unpack('V', substr($binaryData, $offset, 4));
        if ($maintenanceData === false) {
            throw new RuntimeException('Failed to read maintenance');
        }

        $result['maintenance'] = $maintenanceData[1];
        $offset += 4;

        // topspeed (uint32)
        $topspeedData = unpack('V', substr($binaryData, $offset, 4));
        if ($topspeedData === false) {
            throw new RuntimeException('Failed to read topspeed');
        }

        $result['topspeed'] = $topspeedData[1];
        $offset += 4;

        // intro_date (uint16)
        $introDateData = unpack('v', substr($binaryData, $offset, 2));
        if ($introDateData === false) {
            throw new RuntimeException('Failed to read intro_date');
        }

        $result['intro_date'] = $introDateData[1];
        $offset += 2;

        // retire_date (uint16)
        $retireDateData = unpack('v', substr($binaryData, $offset, 2));
        if ($retireDateData === false) {
            throw new RuntimeException('Failed to read retire_date');
        }

        $result['retire_date'] = $retireDateData[1];
        $offset += 2;

        // wtyp (uint8)
        $wtypData = unpack('C', substr($binaryData, $offset, 1));
        if ($wtypData === false) {
            throw new RuntimeException('Failed to read wtyp');
        }

        $result['waytype'] = $wtypData[1];
        $offset += 1;

        // own_wtyp (uint8)
        $ownWtypData = unpack('C', substr($binaryData, $offset, 1));
        if ($ownWtypData === false) {
            throw new RuntimeException('Failed to read own_wtyp');
        }

        $result['own_waytype'] = $ownWtypData[1];

        return $this->buildResult($result);
    }

    /**
     * Parse version 2 (sint64 for price/maintenance)
     *
     * @return array<string, mixed>
     */
    private function parseVersion2(string $binaryData, int $offset): array
    {
        $result = ['version' => 2];

        // price (sint64)
        $result['price'] = $this->readInt64($binaryData, $offset);
        $offset += 8;

        // maintenance (sint64)
        $result['maintenance'] = $this->readInt64($binaryData, $offset);
        $offset += 8;

        // topspeed (uint32)
        $topspeedData = unpack('V', substr($binaryData, $offset, 4));
        if ($topspeedData === false) {
            throw new RuntimeException('Failed to read topspeed');
        }

        $result['topspeed'] = $topspeedData[1];
        $offset += 4;

        // intro_date (uint16)
        $introDateData = unpack('v', substr($binaryData, $offset, 2));
        if ($introDateData === false) {
            throw new RuntimeException('Failed to read intro_date');
        }

        $result['intro_date'] = $introDateData[1];
        $offset += 2;

        // retire_date (uint16)
        $retireDateData = unpack('v', substr($binaryData, $offset, 2));
        if ($retireDateData === false) {
            throw new RuntimeException('Failed to read retire_date');
        }

        $result['retire_date'] = $retireDateData[1];
        $offset += 2;

        // wtyp (uint8)
        $wtypData = unpack('C', substr($binaryData, $offset, 1));
        if ($wtypData === false) {
            throw new RuntimeException('Failed to read wtyp');
        }

        $result['waytype'] = $wtypData[1];
        $offset += 1;

        // own_wtyp (uint8)
        $ownWtypData = unpack('C', substr($binaryData, $offset, 1));
        if ($ownWtypData === false) {
            throw new RuntimeException('Failed to read own_wtyp');
        }

        $result['own_waytype'] = $ownWtypData[1];

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
     * Build final result with human-readable strings
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function buildResult(array $data): array
    {
        // Add waytype string using WayTypeConverter
        $wtyp = $data['waytype'] ?? 0;
        $ownWtyp = $data['own_waytype'] ?? 0;

        $data['waytype_str'] = WayTypeConverter::getWayTypeName(is_int($wtyp) ? $wtyp : 0);
        $data['own_waytype_str'] = WayTypeConverter::getWayTypeName(is_int($ownWtyp) ? $ownWtyp : 0);

        return $data;
    }
}
