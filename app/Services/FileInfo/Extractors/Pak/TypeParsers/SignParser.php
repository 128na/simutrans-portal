<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\SimutransDefaults;
use App\Services\FileInfo\Extractors\Pak\VersionStamp;

/**
 * Parser for roadsign/signal (ROSG) nodes
 *
 * Road signs and railway signals control traffic flow.
 * Supported versions: 1-6
 */
class SignParser implements TypeParserInterface
{
    private const int MAX_SUPPORTED_VERSION = 6;

    // Signal type flags (from roadsign_desc.h)
    private const FLAG_ONE_WAY = 1 << 0;

    private const FLAG_CHOOSE_SIGN = 1 << 1;

    private const FLAG_PRIVATE_ROAD = 1 << 2;

    private const FLAG_SIGN_SIGNAL = 1 << 3;

    private const FLAG_SIGN_PRE_SIGNAL = 1 << 4;

    private const FLAG_SIGN_LONGBLOCK_SIGNAL = 1 << 6;

    private const FLAG_END_OF_CHOOSE_AREA = 1 << 7;

    private const FLAG_SIGN_PRIORITY_SIGNAL = 1 << 8;

    public function canParse(Node $node): bool
    {
        return $node->type === Node::OBJ_ROADSIGN;
    }

    /**
     * @return array<string, mixed>
     */
    public function parse(Node $node): array
    {
        $binaryData = $node->data;
        $stamp = VersionStamp::from($binaryData);

        if (! $stamp->isVersioned) {
            throw InvalidPakFileException::unsupportedTypeVersion('roadsign', 0, self::MAX_SUPPORTED_VERSION);
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
            default => throw InvalidPakFileException::unsupportedTypeVersion('roadsign', $stamp->version, self::MAX_SUPPORTED_VERSION),
        };
    }

    /**
     * Parse version 1 (minimal version)
     *
     * @return array<string, mixed>
     */
    private function parseVersion1(BinaryReader $reader): array
    {
        $result = ['version' => 1];

        $result['min_speed'] = $reader->readUint16LE();
        $result['flags'] = $reader->readUint8();

        // Set defaults for missing fields
        $result['price'] = 50000;
        $result['maintenance'] = 0;
        $result['offset_left'] = 14;
        $result['waytype'] = 1; // road_wt
        $result['intro_date'] = SimutransDefaults::INTRO_YEAR * SimutransDefaults::CURRENT_DATE_BASE;
        $result['retire_date'] = SimutransDefaults::RETIRE_YEAR * SimutransDefaults::CURRENT_DATE_BASE;

        return $this->buildResult($result);
    }

    /**
     * Parse version 2 (+ price)
     *
     * @return array<string, mixed>
     */
    private function parseVersion2(BinaryReader $reader): array
    {
        $result = ['version' => 2];

        $result['min_speed'] = $reader->readUint16LE();
        $result['price'] = $reader->readUint32LE();
        $result['flags'] = $reader->readUint8();

        // Set defaults
        $result['maintenance'] = 0;
        $result['offset_left'] = 14;
        $result['waytype'] = 1; // road_wt
        $result['intro_date'] = SimutransDefaults::INTRO_YEAR * SimutransDefaults::CURRENT_DATE_BASE;
        $result['retire_date'] = SimutransDefaults::RETIRE_YEAR * SimutransDefaults::CURRENT_DATE_BASE;

        return $this->buildResult($result);
    }

    /**
     * Parse version 3 (+ waytype, dates)
     *
     * @return array<string, mixed>
     */
    private function parseVersion3(BinaryReader $reader): array
    {
        $result = ['version' => 3];

        $result['min_speed'] = $reader->readUint16LE();
        $result['price'] = $reader->readUint32LE();
        $result['flags'] = $reader->readUint8();
        $result['waytype'] = $reader->readUint8(); // NEW in version 3
        $result['intro_date'] = $reader->readUint16LE(); // NEW in version 3
        $result['retire_date'] = $reader->readUint16LE(); // NEW in version 3

        // Set defaults
        $result['maintenance'] = 0;
        $result['offset_left'] = 14;

        return $this->buildResult($result);
    }

    /**
     * Parse version 4 (+ offset_left, flags upgraded to uint8)
     *
     * @return array<string, mixed>
     */
    private function parseVersion4(BinaryReader $reader): array
    {
        $result = ['version' => 4];

        $result['min_speed'] = $reader->readUint16LE();
        $result['price'] = $reader->readUint32LE();
        $result['flags'] = $reader->readUint8();
        $result['offset_left'] = $reader->readSint8(); // NEW in version 4
        $result['waytype'] = $reader->readUint8();
        $result['intro_date'] = $reader->readUint16LE();
        $result['retire_date'] = $reader->readUint16LE();

        // Set defaults
        $result['maintenance'] = 0;

        return $this->buildResult($result);
    }

    /**
     * Parse version 5 (flags upgraded to uint16)
     *
     * @return array<string, mixed>
     */
    private function parseVersion5(BinaryReader $reader): array
    {
        $result = ['version' => 5];

        $result['min_speed'] = $reader->readUint16LE();
        $result['price'] = $reader->readUint32LE();
        $result['flags'] = $reader->readUint16LE(); // CHANGED in version 5
        $result['offset_left'] = $reader->readSint8();
        $result['waytype'] = $reader->readUint8();
        $result['intro_date'] = $reader->readUint16LE();
        $result['retire_date'] = $reader->readUint16LE();

        // Set defaults
        $result['maintenance'] = 0;

        return $this->buildResult($result);
    }

    /**
     * Parse version 6 (+ maintenance, 64-bit costs)
     *
     * @return array<string, mixed>
     */
    private function parseVersion6(BinaryReader $reader): array
    {
        $result = ['version' => 6];

        $result['min_speed'] = $reader->readUint16LE();
        $result['price'] = $reader->readSint64LE(); // CHANGED in version 6
        $result['maintenance'] = $reader->readSint64LE(); // NEW in version 6
        $result['flags'] = $reader->readUint16LE();
        $result['offset_left'] = $reader->readSint8();
        $result['waytype'] = $reader->readUint8();
        $result['intro_date'] = $reader->readUint16LE();
        $result['retire_date'] = $reader->readUint16LE();

        return $this->buildResult($result);
    }

    /**
     * Build final result with human-readable strings and decoded flags
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function buildResult(array $data): array
    {
        // Decode flags into human-readable boolean properties
        $flags = $data['flags'] ?? 0;
        if (is_int($flags)) {
            $data['is_one_way'] = ($flags & self::FLAG_ONE_WAY) !== 0;
            $data['is_choose_sign'] = ($flags & self::FLAG_CHOOSE_SIGN) !== 0;
            $data['is_private_way'] = ($flags & self::FLAG_PRIVATE_ROAD) !== 0;
            $data['is_signal'] = ($flags & self::FLAG_SIGN_SIGNAL) !== 0;
            $data['is_pre_signal'] = ($flags & self::FLAG_SIGN_PRE_SIGNAL) !== 0;
            $data['is_longblock_signal'] = ($flags & self::FLAG_SIGN_LONGBLOCK_SIGNAL) !== 0;
            $data['is_priority_signal'] = ($flags & self::FLAG_SIGN_PRIORITY_SIGNAL) !== 0;
            $data['is_end_of_choose'] = ($flags & self::FLAG_END_OF_CHOOSE_AREA) !== 0;

            // Determine sign type string
            $data['sign_type'] = $this->getSignType($flags);
        }

        return $data;
    }

    /**
     * Get human-readable sign type string from flags
     */
    private function getSignType(int $flags): string
    {
        if (($flags & self::FLAG_SIGN_PRIORITY_SIGNAL) !== 0) {
            return 'priority_signal';
        }

        if (($flags & self::FLAG_SIGN_LONGBLOCK_SIGNAL) !== 0) {
            return 'longblock_signal';
        }

        if (($flags & self::FLAG_SIGN_PRE_SIGNAL) !== 0) {
            return 'pre_signal';
        }

        if (($flags & self::FLAG_SIGN_SIGNAL) !== 0) {
            if (($flags & self::FLAG_CHOOSE_SIGN) !== 0) {
                return 'choose_signal';
            }

            return 'signal';
        }

        if (($flags & self::FLAG_END_OF_CHOOSE_AREA) !== 0) {
            return 'end_of_choose';
        }

        if (($flags & self::FLAG_CHOOSE_SIGN) !== 0) {
            return 'choose_sign';
        }

        if (($flags & self::FLAG_PRIVATE_ROAD) !== 0) {
            return 'private_way';
        }

        if (($flags & self::FLAG_ONE_WAY) !== 0) {
            return 'one_way';
        }

        return 'sign';
    }
}
