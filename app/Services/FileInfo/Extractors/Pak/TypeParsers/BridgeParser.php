<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\ObjectTypeConverter;
use App\Services\FileInfo\Extractors\Pak\WayTypeConverter;
use RuntimeException;

/**
 * Parser for bridge (BRDG) nodes
 *
 * Bridges are infrastructure for crossing gaps and obstacles.
 * Supported versions: 0-10
 */
final readonly class BridgeParser implements TypeParserInterface
{
    public function canParse(Node $node): bool
    {
        $objectType = ObjectTypeConverter::toString($node->type);

        return $objectType === 'bridge';
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
            throw new RuntimeException('Failed to read bridge version/waytype');
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
                5 => $this->parseVersion5($binaryData, $offset),
                6 => $this->parseVersion6($binaryData, $offset),
                7, 8 => $this->parseVersion7And8($binaryData, $offset, $version),
                9 => $this->parseVersion9($binaryData, $offset),
                10 => $this->parseVersion10($binaryData, $offset),
                default => throw new RuntimeException("Unsupported bridge version: {$version}"),
            };
        }

        // Version 0 (legacy format): firstUint16 is actually waytype
        return $this->parseVersion0($binaryData, $offset, $firstUint16);
    }

    /**
     * Parse version 0 (legacy - no version stamp)
     *
     * @return array<string, mixed>
     */
    private function parseVersion0(string $binaryData, int $offset, int $wtyp): array
    {
        $result = [
            'version' => 0,
            'wtyp' => $wtyp,
        ];

        // Skip menupos (uint16, deprecated)
        $offset += 2;

        // price (uint32)
        $priceData = unpack('V', substr($binaryData, $offset, 4));
        if ($priceData === false) {
            throw new RuntimeException('Failed to read price');
        }
        $result['price'] = $priceData[1];

        // Set defaults for missing fields
        $result['maintenance'] = 0;
        $result['topspeed'] = 0;
        $result['axle_load'] = 9999;
        $result['pillars_every'] = 0;
        $result['max_length'] = 0;
        $result['max_height'] = 0;
        $result['intro_date'] = 0;
        $result['retire_date'] = 65535;
        $result['pillars_asymmetric'] = false;
        $result['number_of_seasons'] = 0;

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

        // wtyp (uint16, will be cast to uint8)
        $wtypData = unpack('v', substr($binaryData, $offset, 2));
        if ($wtypData === false) {
            throw new RuntimeException('Failed to read wtyp');
        }
        $result['wtyp'] = $wtypData[1] & 0xFF;
        $offset += 2;

        // topspeed (uint16)
        $topspeedData = unpack('v', substr($binaryData, $offset, 2));
        if ($topspeedData === false) {
            throw new RuntimeException('Failed to read topspeed');
        }
        $result['topspeed'] = $topspeedData[1];
        $offset += 2;

        // price (uint32)
        $priceData = unpack('V', substr($binaryData, $offset, 4));
        if ($priceData === false) {
            throw new RuntimeException('Failed to read price');
        }
        $result['price'] = $priceData[1];

        // Set defaults
        $result['maintenance'] = 0;
        $result['axle_load'] = 9999;
        $result['pillars_every'] = 0;
        $result['max_length'] = 0;
        $result['max_height'] = 0;
        $result['intro_date'] = 0;
        $result['retire_date'] = 65535;
        $result['pillars_asymmetric'] = false;
        $result['number_of_seasons'] = 0;

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

        // topspeed (uint16)
        $topspeedData = unpack('v', substr($binaryData, $offset, 2));
        if ($topspeedData === false) {
            throw new RuntimeException('Failed to read topspeed');
        }
        $result['topspeed'] = $topspeedData[1];
        $offset += 2;

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
        $result['wtyp'] = $wtypData[1];

        // Set defaults
        $result['axle_load'] = 9999;
        $result['pillars_every'] = 0;
        $result['max_length'] = 0;
        $result['max_height'] = 0;
        $result['intro_date'] = 0;
        $result['retire_date'] = 65535;
        $result['pillars_asymmetric'] = false;
        $result['number_of_seasons'] = 0;

        return $this->buildResult($result);
    }

    /**
     * Parse version 3
     *
     * @return array<string, mixed>
     */
    private function parseVersion3(string $binaryData, int $offset): array
    {
        $result = $this->parseVersion2($binaryData, $offset);
        $result['version'] = 3;

        // pillars_every (uint8)
        $pillarsData = unpack('C', substr($binaryData, $offset + 11, 1));
        if ($pillarsData === false) {
            throw new RuntimeException('Failed to read pillars_every');
        }
        $result['pillars_every'] = $pillarsData[1];

        return $this->buildResult($result);
    }

    /**
     * Parse version 4
     *
     * @return array<string, mixed>
     */
    private function parseVersion4(string $binaryData, int $offset): array
    {
        $result = $this->parseVersion3($binaryData, $offset);
        $result['version'] = 4;

        // max_length (uint8)
        $maxLengthData = unpack('C', substr($binaryData, $offset + 12, 1));
        if ($maxLengthData === false) {
            throw new RuntimeException('Failed to read max_length');
        }
        $result['max_length'] = $maxLengthData[1];

        return $this->buildResult($result);
    }

    /**
     * Parse version 5
     *
     * @return array<string, mixed>
     */
    private function parseVersion5(string $binaryData, int $offset): array
    {
        $result = $this->parseVersion4($binaryData, $offset);
        $result['version'] = 5;

        // intro_date (uint16)
        $introDateData = unpack('v', substr($binaryData, $offset + 13, 2));
        if ($introDateData === false) {
            throw new RuntimeException('Failed to read intro_date');
        }
        $result['intro_date'] = $introDateData[1];

        // retire_date (uint16)
        $retireDateData = unpack('v', substr($binaryData, $offset + 15, 2));
        if ($retireDateData === false) {
            throw new RuntimeException('Failed to read retire_date');
        }
        $result['retire_date'] = $retireDateData[1];

        return $this->buildResult($result);
    }

    /**
     * Parse version 6
     *
     * @return array<string, mixed>
     */
    private function parseVersion6(string $binaryData, int $offset): array
    {
        $result = $this->parseVersion5($binaryData, $offset);
        $result['version'] = 6;

        // number_of_seasons (uint8)
        $seasonsData = unpack('C', substr($binaryData, $offset + 17, 1));
        if ($seasonsData === false) {
            throw new RuntimeException('Failed to read number_of_seasons');
        }
        $result['number_of_seasons'] = $seasonsData[1];

        return $this->buildResult($result);
    }

    /**
     * Parse versions 7 and 8
     *
     * @return array<string, mixed>
     */
    private function parseVersion7And8(string $binaryData, int $offset, int $version): array
    {
        $result = ['version' => $version];

        // topspeed (uint16)
        $topspeedData = unpack('v', substr($binaryData, $offset, 2));
        if ($topspeedData === false) {
            throw new RuntimeException('Failed to read topspeed');
        }
        $result['topspeed'] = $topspeedData[1];
        $offset += 2;

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
        $result['wtyp'] = $wtypData[1];
        $offset += 1;

        // pillars_every (uint8)
        $pillarsData = unpack('C', substr($binaryData, $offset, 1));
        if ($pillarsData === false) {
            throw new RuntimeException('Failed to read pillars_every');
        }
        $result['pillars_every'] = $pillarsData[1];
        $offset += 1;

        // max_length (uint8)
        $maxLengthData = unpack('C', substr($binaryData, $offset, 1));
        if ($maxLengthData === false) {
            throw new RuntimeException('Failed to read max_length');
        }
        $result['max_length'] = $maxLengthData[1];
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

        // pillars_asymmetric (uint8 as bool)
        $asymmetricData = unpack('C', substr($binaryData, $offset, 1));
        if ($asymmetricData === false) {
            throw new RuntimeException('Failed to read pillars_asymmetric');
        }
        $result['pillars_asymmetric'] = $asymmetricData[1] !== 0;
        $offset += 1;

        // max_height (uint8)
        $maxHeightData = unpack('C', substr($binaryData, $offset, 1));
        if ($maxHeightData === false) {
            throw new RuntimeException('Failed to read max_height');
        }
        $result['max_height'] = $maxHeightData[1];
        $offset += 1;

        // number_of_seasons (uint8)
        $seasonsData = unpack('C', substr($binaryData, $offset, 1));
        if ($seasonsData === false) {
            throw new RuntimeException('Failed to read number_of_seasons');
        }
        $result['number_of_seasons'] = $seasonsData[1];

        // Set defaults
        $result['axle_load'] = 9999;

        return $this->buildResult($result);
    }

    /**
     * Parse version 9
     *
     * @return array<string, mixed>
     */
    private function parseVersion9(string $binaryData, int $offset): array
    {
        $result = ['version' => 9];

        // topspeed (uint16)
        $topspeedData = unpack('v', substr($binaryData, $offset, 2));
        if ($topspeedData === false) {
            throw new RuntimeException('Failed to read topspeed');
        }
        $result['topspeed'] = $topspeedData[1];
        $offset += 2;

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
        $result['wtyp'] = $wtypData[1];
        $offset += 1;

        // pillars_every (uint8)
        $pillarsData = unpack('C', substr($binaryData, $offset, 1));
        if ($pillarsData === false) {
            throw new RuntimeException('Failed to read pillars_every');
        }
        $result['pillars_every'] = $pillarsData[1];
        $offset += 1;

        // max_length (uint8)
        $maxLengthData = unpack('C', substr($binaryData, $offset, 1));
        if ($maxLengthData === false) {
            throw new RuntimeException('Failed to read max_length');
        }
        $result['max_length'] = $maxLengthData[1];
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

        // pillars_asymmetric (uint8 as bool)
        $asymmetricData = unpack('C', substr($binaryData, $offset, 1));
        if ($asymmetricData === false) {
            throw new RuntimeException('Failed to read pillars_asymmetric');
        }
        $result['pillars_asymmetric'] = $asymmetricData[1] !== 0;
        $offset += 1;

        // axle_load (uint16) - NEW in version 9
        $axleLoadData = unpack('v', substr($binaryData, $offset, 2));
        if ($axleLoadData === false) {
            throw new RuntimeException('Failed to read axle_load');
        }
        $result['axle_load'] = $axleLoadData[1];
        $offset += 2;

        // max_height (uint8)
        $maxHeightData = unpack('C', substr($binaryData, $offset, 1));
        if ($maxHeightData === false) {
            throw new RuntimeException('Failed to read max_height');
        }
        $result['max_height'] = $maxHeightData[1];
        $offset += 1;

        // number_of_seasons (uint8)
        $seasonsData = unpack('C', substr($binaryData, $offset, 1));
        if ($seasonsData === false) {
            throw new RuntimeException('Failed to read number_of_seasons');
        }
        $result['number_of_seasons'] = $seasonsData[1];

        return $this->buildResult($result);
    }

    /**
     * Parse version 10 (current)
     *
     * @return array<string, mixed>
     */
    private function parseVersion10(string $binaryData, int $offset): array
    {
        $result = ['version' => 10];

        // topspeed (uint16)
        $topspeedData = unpack('v', substr($binaryData, $offset, 2));
        if ($topspeedData === false) {
            throw new RuntimeException('Failed to read topspeed');
        }
        $result['topspeed'] = $topspeedData[1];
        $offset += 2;

        // price (sint64) - CHANGED in version 10
        $result['price'] = $this->readInt64($binaryData, $offset);
        $offset += 8;

        // maintenance (sint64) - CHANGED in version 10
        $result['maintenance'] = $this->readInt64($binaryData, $offset);
        $offset += 8;

        // wtyp (uint8)
        $wtypData = unpack('C', substr($binaryData, $offset, 1));
        if ($wtypData === false) {
            throw new RuntimeException('Failed to read wtyp');
        }
        $result['wtyp'] = $wtypData[1];
        $offset += 1;

        // pillars_every (uint8)
        $pillarsData = unpack('C', substr($binaryData, $offset, 1));
        if ($pillarsData === false) {
            throw new RuntimeException('Failed to read pillars_every');
        }
        $result['pillars_every'] = $pillarsData[1];
        $offset += 1;

        // max_length (uint8)
        $maxLengthData = unpack('C', substr($binaryData, $offset, 1));
        if ($maxLengthData === false) {
            throw new RuntimeException('Failed to read max_length');
        }
        $result['max_length'] = $maxLengthData[1];
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

        // pillars_asymmetric (uint8 as bool)
        $asymmetricData = unpack('C', substr($binaryData, $offset, 1));
        if ($asymmetricData === false) {
            throw new RuntimeException('Failed to read pillars_asymmetric');
        }
        $result['pillars_asymmetric'] = $asymmetricData[1] !== 0;
        $offset += 1;

        // axle_load (uint16)
        $axleLoadData = unpack('v', substr($binaryData, $offset, 2));
        if ($axleLoadData === false) {
            throw new RuntimeException('Failed to read axle_load');
        }
        $result['axle_load'] = $axleLoadData[1];
        $offset += 2;

        // max_height (uint8)
        $maxHeightData = unpack('C', substr($binaryData, $offset, 1));
        if ($maxHeightData === false) {
            throw new RuntimeException('Failed to read max_height');
        }
        $result['max_height'] = $maxHeightData[1];
        $offset += 1;

        // number_of_seasons (uint8)
        $seasonsData = unpack('C', substr($binaryData, $offset, 1));
        if ($seasonsData === false) {
            throw new RuntimeException('Failed to read number_of_seasons');
        }
        $result['number_of_seasons'] = $seasonsData[1];

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
        // Add waytype string
        $wtyp = $data['wtyp'] ?? 0;
        $data['wtyp_str'] = WayTypeConverter::getWaytypeName(is_int($wtyp) ? $wtyp : 0);

        // Post-processing: if pillars exist and max_height is 0, set it to 7
        $pillarsEvery = $data['pillars_every'] ?? 0;
        $maxHeight = $data['max_height'] ?? 0;
        if (is_int($pillarsEvery) && $pillarsEvery > 0 && is_int($maxHeight) && $maxHeight === 0) {
            $data['max_height'] = 7;
        }

        return $data;
    }
}
