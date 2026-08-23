<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\SimutransDefaults;
use App\Services\FileInfo\Extractors\Pak\VersionStamp;

/**
 * Parser for citycar (private city car) nodes
 *
 * City cars are AI-controlled vehicles that automatically appear in cities.
 * They are not player-owned.
 * Supported versions: 0-2
 */
class CitycarParser implements TypeParserInterface
{
    private const int MAX_SUPPORTED_VERSION = 2;

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
        $stamp = VersionStamp::from($binaryData);

        if ($stamp->isVersioned) {
            $reader = new BinaryReader($binaryData);
            $reader->skip(2);

            return match ($stamp->version) {
                1 => $this->parseVersion1($reader),
                2 => $this->parseVersion2($reader),
                default => throw InvalidPakFileException::unsupportedTypeVersion('citycar', $stamp->version, self::MAX_SUPPORTED_VERSION),
            };
        }

        // Version 0 (legacy): firstUint16 is distribution_weight
        return $this->parseVersion0($stamp->firstUint16);
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
            'intro_date' => SimutransDefaults::INTRO_YEAR * 12,
            'retire_date' => SimutransDefaults::RETIRE_YEAR * 12,
        ];
    }

    /**
     * Parse version 1
     *
     * @return array<string, mixed>
     */
    private function parseVersion1(BinaryReader $reader): array
    {
        $result = ['version' => 1];

        $result['distribution_weight'] = $reader->readUint16LE();

        // topspeed (uint16) - divided by 16 in source, stored as km/h here
        $topspeedRaw = $reader->readUint16LE();
        $result['topspeed'] = intdiv($topspeedRaw, 16);

        // intro_date (uint16) - packed format (year*16 + month)
        $introDateRaw = $reader->readUint16LE();
        $result['intro_date'] = intdiv($introDateRaw, 16) * 12 + ($introDateRaw % 12);

        // retire_date (uint16) - packed format (year*16 + month)
        $retireDateRaw = $reader->readUint16LE();
        $result['retire_date'] = intdiv($retireDateRaw, 16) * 12 + ($retireDateRaw % 12);

        return $result;
    }

    /**
     * Parse version 2
     *
     * @return array<string, mixed>
     */
    private function parseVersion2(BinaryReader $reader): array
    {
        $result = ['version' => 2];

        $result['distribution_weight'] = $reader->readUint16LE();

        // topspeed (uint16) - divided by 16 in source
        $topspeedRaw = $reader->readUint16LE();
        $result['topspeed'] = intdiv($topspeedRaw, 16);

        // intro_date / retire_date (uint16) - direct months format (CHANGED in version 2)
        $result['intro_date'] = $reader->readUint16LE();
        $result['retire_date'] = $reader->readUint16LE();

        return $result;
    }
}
