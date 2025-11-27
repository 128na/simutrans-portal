<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\ObjectTypeConverter;
use App\Services\FileInfo\Extractors\Pak\WayTypeConverter;
use RuntimeException;

/**
 * Parser for crossing (level crossing / railroad crossing) nodes
 *
 * Crossings define where two different way types intersect (e.g., road crossing railway).
 * Supported versions: 1-2 (version 0 is legacy and unsupported)
 */
final readonly class CrossingParser implements TypeParserInterface
{
    public function canParse(Node $node): bool
    {
        $objectType = ObjectTypeConverter::toString($node->type);

        return $objectType === 'crossing';
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
            throw new RuntimeException('Failed to read crossing version');
        }

        $firstUint16 = $firstUint16Data[1];
        $offset += 2;

        // Check if high bit is set (versioned format)
        if (($firstUint16 & 0x8000) === 0) {
            // Version 0 (legacy): Not supported
            throw new RuntimeException('Crossing version 0 (legacy) is not supported');
        }

        $version = $firstUint16 & 0x7FFF;

        return match ($version) {
            1 => $this->parseVersion1($binaryData, $offset),
            2 => $this->parseVersion2($binaryData, $offset),
            default => throw new RuntimeException('Unsupported crossing version: '.$version),
        };
    }

    /**
     * Parse version 1
     *
     * @return array<string, mixed>
     */
    private function parseVersion1(string $binaryData, int $offset): array
    {
        $result = ['version' => 1];

        // waytype1 (uint8)
        $waytype1Data = unpack('C', substr($binaryData, $offset, 1));
        if ($waytype1Data === false) {
            throw new RuntimeException('Failed to read waytype1');
        }

        $result['waytype1'] = $waytype1Data[1];
        $offset += 1;

        // waytype2 (uint8)
        $waytype2Data = unpack('C', substr($binaryData, $offset, 1));
        if ($waytype2Data === false) {
            throw new RuntimeException('Failed to read waytype2');
        }

        $result['waytype2'] = $waytype2Data[1];
        $offset += 1;

        // topspeed1 (uint16)
        $topspeed1Data = unpack('v', substr($binaryData, $offset, 2));
        if ($topspeed1Data === false) {
            throw new RuntimeException('Failed to read topspeed1');
        }

        $result['topspeed1'] = $topspeed1Data[1];
        $offset += 2;

        // topspeed2 (uint16)
        $topspeed2Data = unpack('v', substr($binaryData, $offset, 2));
        if ($topspeed2Data === false) {
            throw new RuntimeException('Failed to read topspeed2');
        }

        $result['topspeed2'] = $topspeed2Data[1];
        $offset += 2;

        // open_animation_time (uint32)
        $openAnimData = unpack('V', substr($binaryData, $offset, 4));
        if ($openAnimData === false) {
            throw new RuntimeException('Failed to read open_animation_time');
        }

        $result['open_animation_time'] = $openAnimData[1];
        $offset += 4;

        // closed_animation_time (uint32)
        $closedAnimData = unpack('V', substr($binaryData, $offset, 4));
        if ($closedAnimData === false) {
            throw new RuntimeException('Failed to read closed_animation_time');
        }

        $result['closed_animation_time'] = $closedAnimData[1];
        $offset += 4;

        // sound (sint8) - note: sound loading is handled in source, we just store the value
        $soundData = unpack('c', substr($binaryData, $offset, 1));
        if ($soundData === false) {
            throw new RuntimeException('Failed to read sound');
        }

        $result['sound'] = $soundData[1];

        // Set defaults for missing fields in version 1
        $result['intro_date'] = 0;
        $result['retire_date'] = 65535;

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

        // waytype1 (uint8)
        $waytype1Data = unpack('C', substr($binaryData, $offset, 1));
        if ($waytype1Data === false) {
            throw new RuntimeException('Failed to read waytype1');
        }

        $result['waytype1'] = $waytype1Data[1];
        $offset += 1;

        // waytype2 (uint8)
        $waytype2Data = unpack('C', substr($binaryData, $offset, 1));
        if ($waytype2Data === false) {
            throw new RuntimeException('Failed to read waytype2');
        }

        $result['waytype2'] = $waytype2Data[1];
        $offset += 1;

        // topspeed1 (uint16)
        $topspeed1Data = unpack('v', substr($binaryData, $offset, 2));
        if ($topspeed1Data === false) {
            throw new RuntimeException('Failed to read topspeed1');
        }

        $result['topspeed1'] = $topspeed1Data[1];
        $offset += 2;

        // topspeed2 (uint16)
        $topspeed2Data = unpack('v', substr($binaryData, $offset, 2));
        if ($topspeed2Data === false) {
            throw new RuntimeException('Failed to read topspeed2');
        }

        $result['topspeed2'] = $topspeed2Data[1];
        $offset += 2;

        // open_animation_time (uint32)
        $openAnimData = unpack('V', substr($binaryData, $offset, 4));
        if ($openAnimData === false) {
            throw new RuntimeException('Failed to read open_animation_time');
        }

        $result['open_animation_time'] = $openAnimData[1];
        $offset += 4;

        // closed_animation_time (uint32)
        $closedAnimData = unpack('V', substr($binaryData, $offset, 4));
        if ($closedAnimData === false) {
            throw new RuntimeException('Failed to read closed_animation_time');
        }

        $result['closed_animation_time'] = $closedAnimData[1];
        $offset += 4;

        // sound (sint8)
        $soundData = unpack('c', substr($binaryData, $offset, 1));
        if ($soundData === false) {
            throw new RuntimeException('Failed to read sound');
        }

        $result['sound'] = $soundData[1];
        $offset += 1;

        // intro_date (uint16) - NEW in version 2
        $introDateData = unpack('v', substr($binaryData, $offset, 2));
        if ($introDateData === false) {
            throw new RuntimeException('Failed to read intro_date');
        }

        $result['intro_date'] = $introDateData[1];
        $offset += 2;

        // retire_date (uint16) - NEW in version 2
        $retireDateData = unpack('v', substr($binaryData, $offset, 2));
        if ($retireDateData === false) {
            throw new RuntimeException('Failed to read retire_date');
        }

        $result['retire_date'] = $retireDateData[1];

        return $this->buildResult($result);
    }

    /**
     * Build final result with human-readable waytype names
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function buildResult(array $data): array
    {
        // Add waytype name strings
        if (isset($data['waytype1'])) {
            assert(is_int($data['waytype1']));
            $data['waytype1_str'] = WayTypeConverter::getWayTypeName($data['waytype1']);
        }

        if (isset($data['waytype2'])) {
            assert(is_int($data['waytype2']));
            $data['waytype2_str'] = WayTypeConverter::getWayTypeName($data['waytype2']);
        }

        return $data;
    }
}
