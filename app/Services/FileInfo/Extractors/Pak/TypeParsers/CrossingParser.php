<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\VersionStamp;

/**
 * Parser for crossing (level crossing / railroad crossing) nodes
 *
 * Crossings define where two different way types intersect (e.g., road crossing railway).
 * Supported versions: 1-2 (version 0 is legacy and unsupported)
 */
class CrossingParser implements TypeParserInterface
{
    // LOAD_SOUND marker - indicates embedded sound file name (sint8)(0xFFFE) = -2
    private const int LOAD_SOUND = -2;

    private const int MAX_SUPPORTED_VERSION = 2;

    public function canParse(Node $node): bool
    {
        return $node->type === Node::OBJ_CROSSING;
    }

    /**
     * @return array<string, mixed>
     */
    public function parse(Node $node): array
    {
        $binaryData = $node->data;
        $stamp = VersionStamp::from($binaryData);

        if (! $stamp->isVersioned) {
            throw InvalidPakFileException::unsupportedTypeVersion('crossing', 0, self::MAX_SUPPORTED_VERSION);
        }

        $reader = new BinaryReader($binaryData);
        $reader->skip(2);

        return match ($stamp->version) {
            1 => $this->parseVersion1($reader),
            2 => $this->parseVersion2($reader),
            default => throw InvalidPakFileException::unsupportedTypeVersion('crossing', $stamp->version, self::MAX_SUPPORTED_VERSION),
        };
    }

    /**
     * Parse version 1
     *
     * @return array<string, mixed>
     */
    private function parseVersion1(BinaryReader $reader): array
    {
        $result = ['version' => 1];

        $result['waytype1'] = $reader->readUint8();
        $result['waytype2'] = $reader->readUint8();
        $result['topspeed1'] = $reader->readUint16LE();
        $result['topspeed2'] = $reader->readUint16LE();
        $result['open_animation_time'] = $reader->readUint32LE();
        $result['closed_animation_time'] = $reader->readUint32LE();
        $result['sound'] = $reader->readSint8();

        // Handle LOAD_SOUND - embedded sound file name
        // Note: intro_date and retire_date are defaults, not read from data in version 1
        if ($result['sound'] === self::LOAD_SOUND) {
            $soundInfo = $this->readEmbeddedSoundName($reader);
            if ($soundInfo !== null) {
                $result['sound_filename'] = $soundInfo;
            }
        }

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
    private function parseVersion2(BinaryReader $reader): array
    {
        $result = ['version' => 2];

        $result['waytype1'] = $reader->readUint8();
        $result['waytype2'] = $reader->readUint8();
        $result['topspeed1'] = $reader->readUint16LE();
        $result['topspeed2'] = $reader->readUint16LE();
        $result['open_animation_time'] = $reader->readUint32LE();
        $result['closed_animation_time'] = $reader->readUint32LE();
        $result['sound'] = $reader->readSint8();

        // Handle LOAD_SOUND - embedded sound file name
        if ($result['sound'] === self::LOAD_SOUND) {
            $soundInfo = $this->readEmbeddedSoundName($reader);
            if ($soundInfo !== null) {
                $result['sound_filename'] = $soundInfo;
            }
        }

        // intro_date / retire_date - NEW in version 2
        $result['intro_date'] = $reader->readUint16LE();
        $result['retire_date'] = $reader->readUint16LE();

        return $this->buildResult($result);
    }

    /**
     * Read embedded sound file name (for LOAD_SOUND)
     *
     * Format: uint8 len, char[len] wavname
     */
    private function readEmbeddedSoundName(BinaryReader $reader): ?string
    {
        if (! $reader->hasMore(1)) {
            return null;
        }

        $len = $reader->readUint8();

        if ($len === 0 || ! $reader->hasMore($len)) {
            return null;
        }

        $wavname = $reader->readString($len);

        // Remove null terminator if present
        return rtrim($wavname, "\0");
    }

    /**
     * Build final result with human-readable waytype names
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function buildResult(array $data): array
    {
        return $data;
    }
}
