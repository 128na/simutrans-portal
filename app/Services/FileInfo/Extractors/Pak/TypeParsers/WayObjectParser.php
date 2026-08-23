<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;

/**
 * Parser for way-object (WAYOBJ) nodes
 *
 * Way objects are infrastructure placed on ways, primarily overhead lines (catenary).
 * Supported versions: 1-2
 *
 * @see simutrans/descriptor/reader/way_obj_reader.cc
 */
class WayObjectParser implements TypeParserInterface
{
    private const int MAX_SUPPORTED_VERSION = 2;

    public function canParse(Node $node): bool
    {
        return $node->type === Node::OBJ_WAYOBJ;
    }

    /**
     * @return array<string, mixed>
     */
    public function parse(Node $node): array
    {
        $reader = new BinaryReader($node->data);

        // Read version (uint16 with high bit as version marker)
        $versionRaw = $reader->readUint16LE();
        $version = $versionRaw & 0x7FFF; // Mask out high bit

        if ($version === 1) {
            return $this->parseVersion1($reader);
        }

        if ($version === 2) {
            return $this->parseVersion2($reader);
        }

        throw InvalidPakFileException::unsupportedTypeVersion('wayobj', $version, self::MAX_SUPPORTED_VERSION);
    }

    /**
     * Parse version 1 (uint32 for price/maintenance)
     *
     * @return array<string, mixed>
     */
    private function parseVersion1(BinaryReader $reader): array
    {
        $result = ['version' => 1];

        $result['price'] = $reader->readUint32LE();
        $result['maintenance'] = $reader->readUint32LE();
        $result['topspeed'] = $reader->readUint32LE();
        $result['intro_date'] = $reader->readUint16LE();
        $result['retire_date'] = $reader->readUint16LE();
        $result['waytype'] = $reader->readUint8();
        $result['own_waytype'] = $reader->readUint8();

        return $this->buildResult($result);
    }

    /**
     * Parse version 2 (sint64 for price/maintenance)
     *
     * @return array<string, mixed>
     */
    private function parseVersion2(BinaryReader $reader): array
    {
        $result = ['version' => 2];

        $result['price'] = $reader->readSint64LE();
        $result['maintenance'] = $reader->readSint64LE();
        $result['topspeed'] = $reader->readUint32LE();
        $result['intro_date'] = $reader->readUint16LE();
        $result['retire_date'] = $reader->readUint16LE();
        $result['waytype'] = $reader->readUint8();
        $result['own_waytype'] = $reader->readUint8();

        return $this->buildResult($result);
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
