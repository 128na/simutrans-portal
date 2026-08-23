<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\VersionStamp;

/**
 * Parser for tunnel (TUNL) nodes
 *
 * Tunnels allow underground passage through terrain.
 * Supported versions: 1-6
 */
class TunnelParser implements TypeParserInterface
{
    private const int MAX_SUPPORTED_VERSION = 6;

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
        $stamp = VersionStamp::from($binaryData);

        if (! $stamp->isVersioned) {
            throw InvalidPakFileException::unsupportedTypeVersion('tunnel', 0, self::MAX_SUPPORTED_VERSION);
        }

        $reader = new BinaryReader($binaryData);
        $reader->skip(2);

        return match ($stamp->version) {
            1 => $this->parseVersion1($reader),
            2 => $this->parseVersion2($reader),
            3 => $this->parseVersion3($reader),
            4 => $this->parseVersion4($reader),
            5 => $this->parseVersion5($reader),
            6 => $this->parseVersion6($reader),
            default => throw InvalidPakFileException::unsupportedTypeVersion('tunnel', $stamp->version, self::MAX_SUPPORTED_VERSION),
        };
    }

    /**
     * Parse version 1 (base version)
     *
     * @return array<string, mixed>
     */
    private function parseVersion1(BinaryReader $reader): array
    {
        $result = ['version' => 1];

        $result['topspeed'] = $reader->readUint32LE();
        $result['price'] = $reader->readUint32LE();
        $result['maintenance'] = $reader->readUint32LE();
        $result['waytype'] = $reader->readUint8();
        $result['intro_date'] = $reader->readUint16LE();
        $result['retire_date'] = $reader->readUint16LE();

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
    private function parseVersion2(BinaryReader $reader): array
    {
        $result = ['version' => 2];

        $result['topspeed'] = $reader->readUint32LE();
        $result['price'] = $reader->readUint32LE();
        $result['maintenance'] = $reader->readUint32LE();
        $result['waytype'] = $reader->readUint8();
        $result['intro_date'] = $reader->readUint16LE();
        $result['retire_date'] = $reader->readUint16LE();
        $result['number_of_seasons'] = $reader->readUint8(); // NEW in version 2

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
    private function parseVersion3(BinaryReader $reader): array
    {
        $result = ['version' => 3];

        $result['topspeed'] = $reader->readUint32LE();
        $result['price'] = $reader->readUint32LE();
        $result['maintenance'] = $reader->readUint32LE();
        $result['waytype'] = $reader->readUint8();
        $result['intro_date'] = $reader->readUint16LE();
        $result['retire_date'] = $reader->readUint16LE();
        $result['number_of_seasons'] = $reader->readUint8();
        $result['has_way'] = $reader->readUint8() !== 0; // NEW in version 3

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
    private function parseVersion4(BinaryReader $reader): array
    {
        $result = ['version' => 4];

        $result['topspeed'] = $reader->readUint32LE();
        $result['price'] = $reader->readUint32LE();
        $result['maintenance'] = $reader->readUint32LE();
        $result['waytype'] = $reader->readUint8();
        $result['intro_date'] = $reader->readUint16LE();
        $result['retire_date'] = $reader->readUint16LE();
        $result['number_of_seasons'] = $reader->readUint8();
        $result['has_way'] = $reader->readUint8() !== 0;
        $result['broad_portals'] = $reader->readUint8() !== 0; // NEW in version 4

        // Set defaults
        $result['axle_load'] = 9999;

        return $this->buildResult($result);
    }

    /**
     * Parse version 5 (+ axle load, current writer version)
     *
     * @return array<string, mixed>
     */
    private function parseVersion5(BinaryReader $reader): array
    {
        $result = ['version' => 5];

        $result['topspeed'] = $reader->readUint32LE();
        $result['price'] = $reader->readUint32LE();
        $result['maintenance'] = $reader->readUint32LE();
        $result['waytype'] = $reader->readUint8();
        $result['intro_date'] = $reader->readUint16LE();
        $result['retire_date'] = $reader->readUint16LE();
        $result['axle_load'] = $reader->readUint16LE(); // NEW in version 5
        $result['number_of_seasons'] = $reader->readUint8();
        $result['has_way'] = $reader->readUint8() !== 0;
        $result['broad_portals'] = $reader->readUint8() !== 0;

        return $this->buildResult($result);
    }

    /**
     * Parse version 6 (+ 64-bit costs, reader-only version)
     *
     * @return array<string, mixed>
     */
    private function parseVersion6(BinaryReader $reader): array
    {
        $result = ['version' => 6];

        $result['topspeed'] = $reader->readUint32LE();
        $result['price'] = $reader->readSint64LE(); // CHANGED in version 6
        $result['maintenance'] = $reader->readSint64LE(); // CHANGED in version 6
        $result['waytype'] = $reader->readUint8();
        $result['intro_date'] = $reader->readUint16LE();
        $result['retire_date'] = $reader->readUint16LE();
        $result['axle_load'] = $reader->readUint16LE();
        $result['number_of_seasons'] = $reader->readUint8();
        $result['has_way'] = $reader->readUint8() !== 0;
        $result['broad_portals'] = $reader->readUint8() !== 0;

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
