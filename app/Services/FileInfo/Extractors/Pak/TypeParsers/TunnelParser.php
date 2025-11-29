<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Services\FileInfo\Extractors\Pak\Node;
use RuntimeException;

/**
 * Parser for tunnel (TUNL) nodes
 *
 * Tunnels allow underground passage through terrain.
 * Supported versions: 1-6
 */
final readonly class TunnelParser implements TypeParserInterface
{
    public function canParse(Node $node): bool
    {
        return $node->type === Node::OBJ_TUNNEL;
    }

    /**
     * @return array<string, mixed>
     */
    public function parse(Node $node): array
    {
        $binaryData = $node->data;
        $offset = 0;

        // Read first uint16 to determine version
        $firstUint16Data = unpack('v', substr($binaryData, $offset, 2));
        if ($firstUint16Data === false) {
            throw new RuntimeException('Failed to read tunnel version');
        }

        $firstUint16 = $firstUint16Data[1];

        // Check if high bit is set (versioned format)
        if (($firstUint16 & 0x8000) === 0) {
            throw new RuntimeException('Tunnel version 0 (legacy) is not supported');
        }

        $version = $firstUint16 & 0x7FFF; // Mask out high bit
        $offset += 2;

        return match ($version) {
            1 => $this->parseVersion1($binaryData, $offset),
            2 => $this->parseVersion2($binaryData, $offset),
            3 => $this->parseVersion3($binaryData, $offset),
            4 => $this->parseVersion4($binaryData, $offset),
            5 => $this->parseVersion5($binaryData, $offset),
            6 => $this->parseVersion6($binaryData, $offset),
            default => throw new RuntimeException('Unsupported tunnel version: '.$version),
        };
    }

    /**
     * Parse version 1 (base version)
     *
     * @return array<string, mixed>
     */
    private function parseVersion1(string $binaryData, int $offset): array
    {
        $result = ['version' => 1];

        // topspeed (uint32)
        $topspeedData = unpack('V', substr($binaryData, $offset, 4));
        if ($topspeedData === false) {
            throw new RuntimeException('Failed to read topspeed');
        }

        $result['topspeed'] = $topspeedData[1];
        $offset += 4;

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

        // wtyp (uint8)
        $wtypData = unpack('C', substr($binaryData, $offset, 1));
        if ($wtypData === false) {
            throw new RuntimeException('Failed to read wtyp');
        }

        $result['waytype'] = $wtypData[1];
        $offset += 1;

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

        // Set defaults for missing fields
        $result['axle_load'] = 9999;
        $result['number_of_seasons'] = 0;
        $result['has_way'] = false;
        $result['broad_portals'] = false;

        return $this->buildResult($result);
    }

    /**
     * Parse version 2 (+ seasonal graphics)
     *
     * @return array<string, mixed>
     */
    private function parseVersion2(string $binaryData, int $offset): array
    {
        $result = ['version' => 2];

        // topspeed (uint32)
        $topspeedData = unpack('V', substr($binaryData, $offset, 4));
        if ($topspeedData === false) {
            throw new RuntimeException('Failed to read topspeed');
        }

        $result['topspeed'] = $topspeedData[1];
        $offset += 4;

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

        // wtyp (uint8)
        $wtypData = unpack('C', substr($binaryData, $offset, 1));
        if ($wtypData === false) {
            throw new RuntimeException('Failed to read wtyp');
        }

        $result['waytype'] = $wtypData[1];
        $offset += 1;

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

        // number_of_seasons (uint8) - NEW in version 2
        $seasonsData = unpack('C', substr($binaryData, $offset, 1));
        if ($seasonsData === false) {
            throw new RuntimeException('Failed to read number_of_seasons');
        }

        $result['number_of_seasons'] = $seasonsData[1];

        // Set defaults
        $result['axle_load'] = 9999;
        $result['has_way'] = false;
        $result['broad_portals'] = false;

        return $this->buildResult($result);
    }

    /**
     * Parse version 3 (+ underground way graphics)
     *
     * @return array<string, mixed>
     */
    private function parseVersion3(string $binaryData, int $offset): array
    {
        $result = ['version' => 3];

        // topspeed (uint32)
        $topspeedData = unpack('V', substr($binaryData, $offset, 4));
        if ($topspeedData === false) {
            throw new RuntimeException('Failed to read topspeed');
        }

        $result['topspeed'] = $topspeedData[1];
        $offset += 4;

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

        // wtyp (uint8)
        $wtypData = unpack('C', substr($binaryData, $offset, 1));
        if ($wtypData === false) {
            throw new RuntimeException('Failed to read wtyp');
        }

        $result['waytype'] = $wtypData[1];
        $offset += 1;

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

        // number_of_seasons (uint8)
        $seasonsData = unpack('C', substr($binaryData, $offset, 1));
        if ($seasonsData === false) {
            throw new RuntimeException('Failed to read number_of_seasons');
        }

        $result['number_of_seasons'] = $seasonsData[1];
        $offset += 1;

        // has_way (uint8 as bool) - NEW in version 3
        $hasWayData = unpack('C', substr($binaryData, $offset, 1));
        if ($hasWayData === false) {
            throw new RuntimeException('Failed to read has_way');
        }

        $result['has_way'] = $hasWayData[1] !== 0;

        // Set defaults
        $result['axle_load'] = 9999;
        $result['broad_portals'] = false;

        return $this->buildResult($result);
    }

    /**
     * Parse version 4 (+ broad portals)
     *
     * @return array<string, mixed>
     */
    private function parseVersion4(string $binaryData, int $offset): array
    {
        $result = ['version' => 4];

        // topspeed (uint32)
        $topspeedData = unpack('V', substr($binaryData, $offset, 4));
        if ($topspeedData === false) {
            throw new RuntimeException('Failed to read topspeed');
        }

        $result['topspeed'] = $topspeedData[1];
        $offset += 4;

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

        // wtyp (uint8)
        $wtypData = unpack('C', substr($binaryData, $offset, 1));
        if ($wtypData === false) {
            throw new RuntimeException('Failed to read wtyp');
        }

        $result['waytype'] = $wtypData[1];
        $offset += 1;

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

        // number_of_seasons (uint8)
        $seasonsData = unpack('C', substr($binaryData, $offset, 1));
        if ($seasonsData === false) {
            throw new RuntimeException('Failed to read number_of_seasons');
        }

        $result['number_of_seasons'] = $seasonsData[1];
        $offset += 1;

        // has_way (uint8 as bool)
        $hasWayData = unpack('C', substr($binaryData, $offset, 1));
        if ($hasWayData === false) {
            throw new RuntimeException('Failed to read has_way');
        }

        $result['has_way'] = $hasWayData[1] !== 0;
        $offset += 1;

        // broad_portals (uint8 as bool) - NEW in version 4
        $broadPortalsData = unpack('C', substr($binaryData, $offset, 1));
        if ($broadPortalsData === false) {
            throw new RuntimeException('Failed to read broad_portals');
        }

        $result['broad_portals'] = $broadPortalsData[1] !== 0;

        // Set defaults
        $result['axle_load'] = 9999;

        return $this->buildResult($result);
    }

    /**
     * Parse version 5 (+ axle load, current writer version)
     *
     * @return array<string, mixed>
     */
    private function parseVersion5(string $binaryData, int $offset): array
    {
        $result = ['version' => 5];

        // topspeed (uint32)
        $topspeedData = unpack('V', substr($binaryData, $offset, 4));
        if ($topspeedData === false) {
            throw new RuntimeException('Failed to read topspeed');
        }

        $result['topspeed'] = $topspeedData[1];
        $offset += 4;

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

        // wtyp (uint8)
        $wtypData = unpack('C', substr($binaryData, $offset, 1));
        if ($wtypData === false) {
            throw new RuntimeException('Failed to read wtyp');
        }

        $result['waytype'] = $wtypData[1];
        $offset += 1;

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

        // axle_load (uint16) - NEW in version 5
        $axleLoadData = unpack('v', substr($binaryData, $offset, 2));
        if ($axleLoadData === false) {
            throw new RuntimeException('Failed to read axle_load');
        }

        $result['axle_load'] = $axleLoadData[1];
        $offset += 2;

        // number_of_seasons (uint8)
        $seasonsData = unpack('C', substr($binaryData, $offset, 1));
        if ($seasonsData === false) {
            throw new RuntimeException('Failed to read number_of_seasons');
        }

        $result['number_of_seasons'] = $seasonsData[1];
        $offset += 1;

        // has_way (uint8 as bool)
        $hasWayData = unpack('C', substr($binaryData, $offset, 1));
        if ($hasWayData === false) {
            throw new RuntimeException('Failed to read has_way');
        }

        $result['has_way'] = $hasWayData[1] !== 0;
        $offset += 1;

        // broad_portals (uint8 as bool)
        $broadPortalsData = unpack('C', substr($binaryData, $offset, 1));
        if ($broadPortalsData === false) {
            throw new RuntimeException('Failed to read broad_portals');
        }

        $result['broad_portals'] = $broadPortalsData[1] !== 0;

        return $this->buildResult($result);
    }

    /**
     * Parse version 6 (+ 64-bit costs, reader-only version)
     *
     * @return array<string, mixed>
     */
    private function parseVersion6(string $binaryData, int $offset): array
    {
        $result = ['version' => 6];

        // topspeed (uint32)
        $topspeedData = unpack('V', substr($binaryData, $offset, 4));
        if ($topspeedData === false) {
            throw new RuntimeException('Failed to read topspeed');
        }

        $result['topspeed'] = $topspeedData[1];
        $offset += 4;

        // price (sint64) - CHANGED in version 6
        $result['price'] = $this->readInt64($binaryData, $offset);
        $offset += 8;

        // maintenance (sint64) - CHANGED in version 6
        $result['maintenance'] = $this->readInt64($binaryData, $offset);
        $offset += 8;

        // wtyp (uint8)
        $wtypData = unpack('C', substr($binaryData, $offset, 1));
        if ($wtypData === false) {
            throw new RuntimeException('Failed to read wtyp');
        }

        $result['waytype'] = $wtypData[1];
        $offset += 1;

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

        // axle_load (uint16)
        $axleLoadData = unpack('v', substr($binaryData, $offset, 2));
        if ($axleLoadData === false) {
            throw new RuntimeException('Failed to read axle_load');
        }

        $result['axle_load'] = $axleLoadData[1];
        $offset += 2;

        // number_of_seasons (uint8)
        $seasonsData = unpack('C', substr($binaryData, $offset, 1));
        if ($seasonsData === false) {
            throw new RuntimeException('Failed to read number_of_seasons');
        }

        $result['number_of_seasons'] = $seasonsData[1];
        $offset += 1;

        // has_way (uint8 as bool)
        $hasWayData = unpack('C', substr($binaryData, $offset, 1));
        if ($hasWayData === false) {
            throw new RuntimeException('Failed to read has_way');
        }

        $result['has_way'] = $hasWayData[1] !== 0;
        $offset += 1;

        // broad_portals (uint8 as bool)
        $broadPortalsData = unpack('C', substr($binaryData, $offset, 1));
        if ($broadPortalsData === false) {
            throw new RuntimeException('Failed to read broad_portals');
        }

        $result['broad_portals'] = $broadPortalsData[1] !== 0;

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
        return $data;
    }
}
