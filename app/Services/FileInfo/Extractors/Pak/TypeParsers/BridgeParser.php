<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\VersionStamp;

/**
 * Parser for bridge (BRDG) nodes
 *
 * Bridges are infrastructure for crossing gaps and obstacles.
 * Supported versions: 0-11
 */
class BridgeParser implements TypeParserInterface
{
    private const int MAX_SUPPORTED_VERSION = 11;

    public function canParse(Node $node): bool
    {
        return $node->type === Node::OBJ_BRIDGE;
    }

    /**
     * @return array<string, mixed>
     */
    public function parse(Node $node): array
    {
        $binaryData = $node->data;
        $stamp = VersionStamp::from($binaryData);
        $reader = new BinaryReader($binaryData);

        if ($stamp->isVersioned) {
            $reader->skip(2);
            $version = $stamp->version;

            $data = match ($version) {
                1 => $this->parseVersion1($reader),
                2 => $this->parseVersion2($reader),
                3 => $this->parseVersion3($reader),
                4 => $this->parseVersion4($reader),
                5 => $this->parseVersion5($reader),
                6 => $this->parseVersion6($reader),
                7, 8 => $this->parseVersion7And8($reader, $version),
                9 => $this->parseVersion9($reader),
                10 => $this->parseVersion10($reader),
                11 => $this->parseVersion11($reader),
                default => throw InvalidPakFileException::unsupportedTypeVersion('bridge', $version, self::MAX_SUPPORTED_VERSION),
            };
        } else {
            // Version 0 (legacy format): firstUint16 is actually waytype
            $version = 0;
            $data = $this->parseVersion0($reader, $stamp->firstUint16);
        }

        // clip_below defaults based on waytype for versions < 11 (from bridge_reader.cc)
        if ($version < 11) {
            $data['clip_below'] = ($data['waytype'] ?? 0) !== 128; // 128 = powerline_wt
        }

        return $data;
    }

    /**
     * Parse version 0 (legacy - no version stamp)
     *
     * @return array<string, mixed>
     */
    private function parseVersion0(BinaryReader $reader, int $wtyp): array
    {
        $result = [
            'version' => 0,
            'waytype' => $wtyp,
        ];

        // Skip menupos (uint16, deprecated)
        $reader->skip(2);

        $result['price'] = $reader->readUint32LE();

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
    private function parseVersion1(BinaryReader $reader): array
    {
        $result = ['version' => 1];

        // wtyp (uint16, will be cast to uint8)
        $result['waytype'] = $reader->readUint16LE() & 0xFF;
        $result['topspeed'] = $reader->readUint16LE();
        $result['price'] = $reader->readUint32LE();

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
    private function parseVersion2(BinaryReader $reader): array
    {
        $result = ['version' => 2];

        $result['topspeed'] = $reader->readUint16LE();
        $result['price'] = $reader->readUint32LE();
        $result['maintenance'] = $reader->readUint32LE();
        $result['waytype'] = $reader->readUint8();

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
    private function parseVersion3(BinaryReader $reader): array
    {
        $result = $this->parseVersion2($reader);
        $result['version'] = 3;

        $result['pillars_every'] = $reader->readUint8();

        return $this->buildResult($result);
    }

    /**
     * Parse version 4
     *
     * @return array<string, mixed>
     */
    private function parseVersion4(BinaryReader $reader): array
    {
        $result = $this->parseVersion3($reader);
        $result['version'] = 4;

        $result['max_length'] = $reader->readUint8();

        return $this->buildResult($result);
    }

    /**
     * Parse version 5
     *
     * @return array<string, mixed>
     */
    private function parseVersion5(BinaryReader $reader): array
    {
        $result = $this->parseVersion4($reader);
        $result['version'] = 5;

        $result['intro_date'] = $reader->readUint16LE();
        $result['retire_date'] = $reader->readUint16LE();

        return $this->buildResult($result);
    }

    /**
     * Parse version 6
     *
     * @return array<string, mixed>
     */
    private function parseVersion6(BinaryReader $reader): array
    {
        $result = $this->parseVersion5($reader);
        $result['version'] = 6;

        $result['number_of_seasons'] = $reader->readUint8();

        return $this->buildResult($result);
    }

    /**
     * Parse versions 7 and 8
     *
     * @return array<string, mixed>
     */
    private function parseVersion7And8(BinaryReader $reader, int $version): array
    {
        $result = ['version' => $version];

        $result['topspeed'] = $reader->readUint16LE();
        $result['price'] = $reader->readUint32LE();
        $result['maintenance'] = $reader->readUint32LE();
        $result['waytype'] = $reader->readUint8();
        $result['pillars_every'] = $reader->readUint8();
        $result['max_length'] = $reader->readUint8();
        $result['intro_date'] = $reader->readUint16LE();
        $result['retire_date'] = $reader->readUint16LE();
        $result['pillars_asymmetric'] = $reader->readUint8() !== 0;
        $result['max_height'] = $reader->readUint8();
        $result['number_of_seasons'] = $reader->readUint8();

        // Set defaults
        $result['axle_load'] = 9999;

        return $this->buildResult($result);
    }

    /**
     * Parse version 9
     *
     * @return array<string, mixed>
     */
    private function parseVersion9(BinaryReader $reader): array
    {
        $result = ['version' => 9];

        $result['topspeed'] = $reader->readUint16LE();
        $result['price'] = $reader->readUint32LE();
        $result['maintenance'] = $reader->readUint32LE();
        $result['waytype'] = $reader->readUint8();
        $result['pillars_every'] = $reader->readUint8();
        $result['max_length'] = $reader->readUint8();
        $result['intro_date'] = $reader->readUint16LE();
        $result['retire_date'] = $reader->readUint16LE();
        $result['pillars_asymmetric'] = $reader->readUint8() !== 0;
        $result['axle_load'] = $reader->readUint16LE(); // NEW in version 9
        $result['max_height'] = $reader->readUint8();
        $result['number_of_seasons'] = $reader->readUint8();

        return $this->buildResult($result);
    }

    /**
     * Parse version 10 (current)
     *
     * @return array<string, mixed>
     */
    private function parseVersion10(BinaryReader $reader): array
    {
        $result = ['version' => 10];

        $result['topspeed'] = $reader->readUint16LE();
        $result['price'] = $reader->readSint64LE(); // CHANGED in version 10
        $result['maintenance'] = $reader->readSint64LE(); // CHANGED in version 10
        $result['waytype'] = $reader->readUint8();
        $result['pillars_every'] = $reader->readUint8();
        $result['max_length'] = $reader->readUint8();
        $result['intro_date'] = $reader->readUint16LE();
        $result['retire_date'] = $reader->readUint16LE();
        $result['pillars_asymmetric'] = $reader->readUint8() !== 0;
        $result['axle_load'] = $reader->readUint16LE();
        $result['max_height'] = $reader->readUint8();
        $result['number_of_seasons'] = $reader->readUint8();

        return $this->buildResult($result);
    }

    /**
     * Parse version 11 (adds clip_below)
     *
     * @return array<string, mixed>
     */
    private function parseVersion11(BinaryReader $reader): array
    {
        $result = ['version' => 11];

        $result['topspeed'] = $reader->readUint16LE();
        $result['price'] = $reader->readSint64LE();
        $result['maintenance'] = $reader->readSint64LE();
        $result['waytype'] = $reader->readUint8();
        $result['pillars_every'] = $reader->readUint8();
        $result['max_length'] = $reader->readUint8();
        $result['intro_date'] = $reader->readUint16LE();
        $result['retire_date'] = $reader->readUint16LE();
        $result['pillars_asymmetric'] = $reader->readUint8() !== 0;
        $result['axle_load'] = $reader->readUint16LE();
        $result['max_height'] = $reader->readUint8();
        $result['clip_below'] = $reader->readUint8() !== 0;
        $result['number_of_seasons'] = $reader->readUint8();

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
        // Post-processing: if pillars exist and max_height is 0, set it to 7
        $pillarsEvery = $data['pillars_every'] ?? 0;
        $maxHeight = $data['max_height'] ?? 0;
        if (is_int($pillarsEvery) && $pillarsEvery > 0 && is_int($maxHeight) && $maxHeight === 0) {
            $data['max_height'] = 7;
        }

        return $data;
    }
}
