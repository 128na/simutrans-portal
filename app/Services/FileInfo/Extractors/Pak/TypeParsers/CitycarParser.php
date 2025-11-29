<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Services\FileInfo\Extractors\Pak\Node;
use RuntimeException;

/**
 * Parser for citycar (private city car) nodes
 *
 * City cars are AI-controlled vehicles that automatically appear in cities.
 * They are not player-owned.
 * Supported versions: 0-2
 */
final readonly class CitycarParser implements TypeParserInterface
{
    // Default intro/retire dates from intro_dates.h
    private const int DEFAULT_INTRO_DATE = 1930;

    private const int DEFAULT_RETIRE_DATE = 2999;

    public function canParse(Node $node): bool
    {
        return $node->type === Node::OBJ_CITYCAR;
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
            throw new RuntimeException('Failed to read citycar version/weight');
        }

        $firstUint16 = $firstUint16Data[1];
        $offset += 2;

        // Check if high bit is set (versioned format)
        if (($firstUint16 & 0x8000) !== 0) {
            $version = $firstUint16 & 0x7FFF;

            return match ($version) {
                1 => $this->parseVersion1($binaryData, $offset),
                2 => $this->parseVersion2($binaryData, $offset),
                default => throw new RuntimeException('Unsupported citycar version: '.$version),
            };
        }

        // Version 0 (legacy): firstUint16 is distribution_weight
        return $this->parseVersion0($firstUint16);
    }

    /**
     * Parse version 0 (legacy - no version stamp)
     *
     * @return array<string, mixed>
     */
    private function parseVersion0(int $distributionWeight): array
    {
        return [
            'version' => 0,
            'distribution_weight' => $distributionWeight,
            'topspeed' => 80, // Default 80 km/h
            'intro_date' => self::DEFAULT_INTRO_DATE * 12,
            'retire_date' => self::DEFAULT_RETIRE_DATE * 12,
        ];
    }

    /**
     * Parse version 1
     *
     * @return array<string, mixed>
     */
    private function parseVersion1(string $binaryData, int $offset): array
    {
        $result = ['version' => 1];

        // distribution_weight (uint16)
        $weightData = unpack('v', substr($binaryData, $offset, 2));
        if ($weightData === false) {
            throw new RuntimeException('Failed to read distribution_weight');
        }

        $result['distribution_weight'] = $weightData[1];
        $offset += 2;

        // topspeed (uint16) - divided by 16 in source, stored as km/h here
        $topspeedData = unpack('v', substr($binaryData, $offset, 2));
        if ($topspeedData === false) {
            throw new RuntimeException('Failed to read topspeed');
        }

        $topspeedRaw = $topspeedData[1];
        $result['topspeed'] = intdiv($topspeedRaw, 16);
        $offset += 2;

        // intro_date (uint16) - packed format (year*16 + month)
        $introDateData = unpack('v', substr($binaryData, $offset, 2));
        if ($introDateData === false) {
            throw new RuntimeException('Failed to read intro_date');
        }

        $introDateRaw = $introDateData[1];
        $result['intro_date'] = intdiv($introDateRaw, 16) * 12 + ($introDateRaw % 12);
        $offset += 2;

        // retire_date (uint16) - packed format (year*16 + month)
        $retireDateData = unpack('v', substr($binaryData, $offset, 2));
        if ($retireDateData === false) {
            throw new RuntimeException('Failed to read retire_date');
        }

        $retireDateRaw = $retireDateData[1];
        $result['retire_date'] = intdiv($retireDateRaw, 16) * 12 + ($retireDateRaw % 12);

        return $result;
    }

    /**
     * Parse version 2
     *
     * @return array<string, mixed>
     */
    private function parseVersion2(string $binaryData, int $offset): array
    {
        $result = ['version' => 2];

        // distribution_weight (uint16)
        $weightData = unpack('v', substr($binaryData, $offset, 2));
        if ($weightData === false) {
            throw new RuntimeException('Failed to read distribution_weight');
        }

        $result['distribution_weight'] = $weightData[1];
        $offset += 2;

        // topspeed (uint16) - divided by 16 in source
        $topspeedData = unpack('v', substr($binaryData, $offset, 2));
        if ($topspeedData === false) {
            throw new RuntimeException('Failed to read topspeed');
        }

        $topspeedRaw = $topspeedData[1];
        $result['topspeed'] = intdiv($topspeedRaw, 16);
        $offset += 2;

        // intro_date (uint16) - direct months format (CHANGED in version 2)
        $introDateData = unpack('v', substr($binaryData, $offset, 2));
        if ($introDateData === false) {
            throw new RuntimeException('Failed to read intro_date');
        }

        $result['intro_date'] = $introDateData[1];
        $offset += 2;

        // retire_date (uint16) - direct months format (CHANGED in version 2)
        $retireDateData = unpack('v', substr($binaryData, $offset, 2));
        if ($retireDateData === false) {
            throw new RuntimeException('Failed to read retire_date');
        }

        $result['retire_date'] = $retireDateData[1];

        return $result;
    }
}
