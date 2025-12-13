<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Services\FileInfo\Extractors\Pak\Node;
use RuntimeException;

/**
 * Parser for roadsign/signal (ROSG) nodes
 *
 * Road signs and railway signals control traffic flow.
 * Supported versions: 1-6
 */
class SignParser implements TypeParserInterface
{
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
        $offset = 0;

        // Read first uint16 to determine version
        $firstUint16Data = unpack('v', substr($binaryData, $offset, 2));
        if ($firstUint16Data === false) {
            throw new RuntimeException('Failed to read roadsign version');
        }

        $firstUint16 = $firstUint16Data[1];

        // Check if high bit is set (versioned format)
        if (($firstUint16 & 0x8000) === 0) {
            throw new RuntimeException('Roadsign version 0 (legacy) is not supported');
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
            default => throw new RuntimeException('Unsupported roadsign version: '.$version),
        };
    }

    /**
     * Parse version 1 (minimal version)
     *
     * @return array<string, mixed>
     */
    private function parseVersion1(string $binaryData, int $offset): array
    {
        $result = ['version' => 1];

        // min_speed (uint16) - kmh
        $minSpeedData = unpack('v', substr($binaryData, $offset, 2));
        if ($minSpeedData === false) {
            throw new RuntimeException('Failed to read min_speed');
        }

        $result['min_speed'] = $minSpeedData[1];
        $offset += 2;

        // flags (uint8)
        $flagsData = unpack('C', substr($binaryData, $offset, 1));
        if ($flagsData === false) {
            throw new RuntimeException('Failed to read flags');
        }

        $result['flags'] = $flagsData[1];

        // Set defaults for missing fields
        $result['price'] = 50000;
        $result['maintenance'] = 0;
        $result['offset_left'] = 14;
        $result['waytype'] = 1; // road_wt
        $result['intro_date'] = 0;
        $result['retire_date'] = 65535;

        return $this->buildResult($result);
    }

    /**
     * Parse version 2 (+ price)
     *
     * @return array<string, mixed>
     */
    private function parseVersion2(string $binaryData, int $offset): array
    {
        $result = ['version' => 2];

        // min_speed (uint16)
        $minSpeedData = unpack('v', substr($binaryData, $offset, 2));
        if ($minSpeedData === false) {
            throw new RuntimeException('Failed to read min_speed');
        }

        $result['min_speed'] = $minSpeedData[1];
        $offset += 2;

        // price (uint32)
        $priceData = unpack('V', substr($binaryData, $offset, 4));
        if ($priceData === false) {
            throw new RuntimeException('Failed to read price');
        }

        $result['price'] = $priceData[1];
        $offset += 4;

        // flags (uint8)
        $flagsData = unpack('C', substr($binaryData, $offset, 1));
        if ($flagsData === false) {
            throw new RuntimeException('Failed to read flags');
        }

        $result['flags'] = $flagsData[1];

        // Set defaults
        $result['maintenance'] = 0;
        $result['offset_left'] = 14;
        $result['waytype'] = 1; // road_wt
        $result['intro_date'] = 0;
        $result['retire_date'] = 65535;

        return $this->buildResult($result);
    }

    /**
     * Parse version 3 (+ waytype, dates)
     *
     * @return array<string, mixed>
     */
    private function parseVersion3(string $binaryData, int $offset): array
    {
        $result = ['version' => 3];

        // min_speed (uint16)
        $minSpeedData = unpack('v', substr($binaryData, $offset, 2));
        if ($minSpeedData === false) {
            throw new RuntimeException('Failed to read min_speed');
        }

        $result['min_speed'] = $minSpeedData[1];
        $offset += 2;

        // price (uint32)
        $priceData = unpack('V', substr($binaryData, $offset, 4));
        if ($priceData === false) {
            throw new RuntimeException('Failed to read price');
        }

        $result['price'] = $priceData[1];
        $offset += 4;

        // flags (uint8)
        $flagsData = unpack('C', substr($binaryData, $offset, 1));
        if ($flagsData === false) {
            throw new RuntimeException('Failed to read flags');
        }

        $result['flags'] = $flagsData[1];
        $offset += 1;

        // wtyp (uint8) - NEW in version 3
        $wtypData = unpack('C', substr($binaryData, $offset, 1));
        if ($wtypData === false) {
            throw new RuntimeException('Failed to read wtyp');
        }

        $result['waytype'] = $wtypData[1];
        $offset += 1;

        // intro_date (uint16) - NEW in version 3
        $introDateData = unpack('v', substr($binaryData, $offset, 2));
        if ($introDateData === false) {
            throw new RuntimeException('Failed to read intro_date');
        }

        $result['intro_date'] = $introDateData[1];
        $offset += 2;

        // retire_date (uint16) - NEW in version 3
        $retireDateData = unpack('v', substr($binaryData, $offset, 2));
        if ($retireDateData === false) {
            throw new RuntimeException('Failed to read retire_date');
        }

        $result['retire_date'] = $retireDateData[1];

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
    private function parseVersion4(string $binaryData, int $offset): array
    {
        $result = ['version' => 4];

        // min_speed (uint16)
        $minSpeedData = unpack('v', substr($binaryData, $offset, 2));
        if ($minSpeedData === false) {
            throw new RuntimeException('Failed to read min_speed');
        }

        $result['min_speed'] = $minSpeedData[1];
        $offset += 2;

        // price (uint32)
        $priceData = unpack('V', substr($binaryData, $offset, 4));
        if ($priceData === false) {
            throw new RuntimeException('Failed to read price');
        }

        $result['price'] = $priceData[1];
        $offset += 4;

        // flags (uint8)
        $flagsData = unpack('C', substr($binaryData, $offset, 1));
        if ($flagsData === false) {
            throw new RuntimeException('Failed to read flags');
        }

        $result['flags'] = $flagsData[1];
        $offset += 1;

        // offset_left (sint8) - NEW in version 4
        $offsetLeftData = unpack('c', substr($binaryData, $offset, 1));
        if ($offsetLeftData === false) {
            throw new RuntimeException('Failed to read offset_left');
        }

        $result['offset_left'] = $offsetLeftData[1];
        $offset += 1;

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

        // Set defaults
        $result['maintenance'] = 0;

        return $this->buildResult($result);
    }

    /**
     * Parse version 5 (flags upgraded to uint16)
     *
     * @return array<string, mixed>
     */
    private function parseVersion5(string $binaryData, int $offset): array
    {
        $result = ['version' => 5];

        // min_speed (uint16)
        $minSpeedData = unpack('v', substr($binaryData, $offset, 2));
        if ($minSpeedData === false) {
            throw new RuntimeException('Failed to read min_speed');
        }

        $result['min_speed'] = $minSpeedData[1];
        $offset += 2;

        // price (uint32)
        $priceData = unpack('V', substr($binaryData, $offset, 4));
        if ($priceData === false) {
            throw new RuntimeException('Failed to read price');
        }

        $result['price'] = $priceData[1];
        $offset += 4;

        // flags (uint16) - CHANGED in version 5
        $flagsData = unpack('v', substr($binaryData, $offset, 2));
        if ($flagsData === false) {
            throw new RuntimeException('Failed to read flags');
        }

        $result['flags'] = $flagsData[1];
        $offset += 2;

        // offset_left (sint8)
        $offsetLeftData = unpack('c', substr($binaryData, $offset, 1));
        if ($offsetLeftData === false) {
            throw new RuntimeException('Failed to read offset_left');
        }

        $result['offset_left'] = $offsetLeftData[1];
        $offset += 1;

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

        // Set defaults
        $result['maintenance'] = 0;

        return $this->buildResult($result);
    }

    /**
     * Parse version 6 (+ maintenance, 64-bit costs)
     *
     * @return array<string, mixed>
     */
    private function parseVersion6(string $binaryData, int $offset): array
    {
        $result = ['version' => 6];

        // min_speed (uint16)
        $minSpeedData = unpack('v', substr($binaryData, $offset, 2));
        if ($minSpeedData === false) {
            throw new RuntimeException('Failed to read min_speed');
        }

        $result['min_speed'] = $minSpeedData[1];
        $offset += 2;

        // price (sint64) - CHANGED in version 6
        $result['price'] = $this->readInt64($binaryData, $offset);
        $offset += 8;

        // maintenance (sint64) - NEW in version 6
        $result['maintenance'] = $this->readInt64($binaryData, $offset);
        $offset += 8;

        // flags (uint16)
        $flagsData = unpack('v', substr($binaryData, $offset, 2));
        if ($flagsData === false) {
            throw new RuntimeException('Failed to read flags');
        }

        $result['flags'] = $flagsData[1];
        $offset += 2;

        // offset_left (sint8)
        $offsetLeftData = unpack('c', substr($binaryData, $offset, 1));
        if ($offsetLeftData === false) {
            throw new RuntimeException('Failed to read offset_left');
        }

        $result['offset_left'] = $offsetLeftData[1];
        $offset += 1;

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
