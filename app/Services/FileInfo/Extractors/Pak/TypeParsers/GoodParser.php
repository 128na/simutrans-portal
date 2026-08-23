<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\TextNodeExtractor;
use App\Services\FileInfo\Extractors\Pak\VersionStamp;

/**
 * Parser for goods/freight (GOOD) nodes
 *
 * Goods define cargo types that can be transported.
 * Supported versions: 0-4
 */
class GoodParser implements TypeParserInterface
{
    private const int MAX_SUPPORTED_VERSION = 4;

    public function canParse(Node $node): bool
    {
        return $node->type === Node::OBJ_GOOD;
    }

    /**
     * @return array<string, mixed>
     */
    public function parse(Node $node): array
    {
        // Extract metric from TEXT node (child node 2)
        $metric = null;
        $metricNode = $node->getChild(2);
        if ($metricNode instanceof Node && $metricNode->isType(Node::OBJ_TEXT)) {
            $metric = TextNodeExtractor::extract($metricNode);
            if ($metric === '') {
                $metric = null;
            }
        }

        $binaryData = $node->data;
        $stamp = VersionStamp::from($binaryData);
        $reader = new BinaryReader($binaryData);

        if ($stamp->isVersioned) {
            $reader->skip(2);
            $result = match ($stamp->version) {
                1 => $this->parseVersion1($reader),
                2 => $this->parseVersion2($reader),
                3 => $this->parseVersion3($reader),
                4 => $this->parseVersion4($reader),
                default => throw InvalidPakFileException::unsupportedTypeVersion('good', $stamp->version, self::MAX_SUPPORTED_VERSION),
            };
        } else {
            // Version 0 (legacy format): firstUint16 is actually base_value
            $result = $this->parseVersion0($reader, $stamp->firstUint16);
        }

        // Add metric to result
        if ($metric !== null) {
            $result['metric'] = $metric;
        }

        return $result;
    }

    /**
     * Parse version 0 (legacy - no version stamp)
     *
     * @return array<string, mixed>
     */
    private function parseVersion0(BinaryReader $reader, int $baseValue): array
    {
        $result = [
            'version' => 0,
            'base_value' => $baseValue,
        ];

        $result['catg'] = $reader->readUint16LE() & 0xFF; // Cast to uint8

        // Set defaults for missing fields
        $result['speed_bonus'] = 0;
        $result['weight_per_unit'] = 100;
        $result['color'] = 255;

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

        $result['base_value'] = $reader->readUint16LE();
        $result['catg'] = $reader->readUint16LE() & 0xFF;
        $result['speed_bonus'] = $reader->readUint16LE(); // NEW in version 1

        // Set defaults
        $result['weight_per_unit'] = 100;
        $result['color'] = 255;

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

        $result['base_value'] = $reader->readUint16LE();
        $result['catg'] = $reader->readUint16LE() & 0xFF;
        $result['speed_bonus'] = $reader->readUint16LE();
        $result['weight_per_unit'] = $reader->readUint16LE(); // NEW in version 2

        // Set defaults
        $result['color'] = 255;

        return $this->buildResult($result);
    }

    /**
     * Parse version 3
     *
     * @return array<string, mixed>
     */
    private function parseVersion3(BinaryReader $reader): array
    {
        $result = ['version' => 3];

        $result['base_value'] = $reader->readUint16LE();
        $result['catg'] = $reader->readUint8(); // CHANGED in version 3
        $result['speed_bonus'] = $reader->readUint16LE();
        $result['weight_per_unit'] = $reader->readUint16LE();
        $result['color'] = $reader->readUint8(); // NEW in version 3

        return $this->buildResult($result);
    }

    /**
     * Parse version 4 (64-bit base_value)
     *
     * @return array<string, mixed>
     */
    private function parseVersion4(BinaryReader $reader): array
    {
        $result = ['version' => 4];

        $result['base_value'] = $reader->readSint64LE(); // CHANGED in version 4
        $result['catg'] = $reader->readUint8();
        $result['speed_bonus'] = $reader->readUint16LE();
        $result['weight_per_unit'] = $reader->readUint16LE();
        $result['color'] = $reader->readUint8();

        return $this->buildResult($result);
    }

    /**
     * Build final result with human-readable category name
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function buildResult(array $data): array
    {
        return $data;
    }
}
