<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\ObjectTypeConverter;
use App\Services\FileInfo\Extractors\Pak\TextNodeExtractor;
use RuntimeException;

/**
 * Parser for factory (industrial building) nodes
 *
 * Factories produce and consume goods. They are the economic heart of Simutrans.
 * Supported versions: 0-5
 *
 * Note: This parser only extracts the main factory metadata.
 * Child nodes (suppliers, products, fields, smoke) are not parsed.
 */
final readonly class FactoryParser implements TypeParserInterface
{
    public function canParse(Node $node): bool
    {
        $objectType = ObjectTypeConverter::toString($node->type);

        return $objectType === 'factory';
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
            throw new RuntimeException('Failed to read factory version/placement');
        }

        $firstUint16 = $firstUint16Data[1];
        $offset += 2;

        // Check if high bit is set (versioned format)
        if (($firstUint16 & 0x8000) !== 0) {
            $version = $firstUint16 & 0x7FFF;

            $result = match ($version) {
                1 => $this->parseVersion1($binaryData, $offset),
                2 => $this->parseVersion2($binaryData, $offset),
                3 => $this->parseVersion3($binaryData, $offset),
                4 => $this->parseVersion4($binaryData, $offset),
                5 => $this->parseVersion5($binaryData, $offset),
                default => throw new RuntimeException('Unsupported factory version: '.$version),
            };
        } else {
            // Version 0 (legacy): firstUint16 is placement type
            $result = $this->parseVersion0($binaryData, $offset, $firstUint16);
        }

        // Extract input data from FSUP (factory supplier) child nodes
        $result['input'] = $this->extractInputFromChildren($node);

        // Extract output data from FPRO (factory product) child nodes
        $result['output'] = $this->extractOutputFromChildren($node);

        return $result;
    }

    /**
     * Parse version 0 (legacy - no version stamp)
     *
     * @return array<string, mixed>
     */
    private function parseVersion0(string $binaryData, int $offset, int $placement): array
    {
        $result = [
            'version' => 0,
            'placement' => $placement,
        ];

        // Skip always-zero field
        $offset += 2;

        // productivity (uint16) with high bit set
        $productivityData = unpack('v', substr($binaryData, $offset, 2));
        if ($productivityData === false) {
            throw new RuntimeException('Failed to read productivity');
        }

        $result['productivity'] = $productivityData[1] | 0x8000;
        $offset += 2;

        // range (uint16)
        $rangeData = unpack('v', substr($binaryData, $offset, 2));
        if ($rangeData === false) {
            throw new RuntimeException('Failed to read range');
        }

        $result['range'] = $rangeData[1];
        $offset += 2;

        // distribution_weight (uint16)
        $weightData = unpack('v', substr($binaryData, $offset, 2));
        if ($weightData === false) {
            throw new RuntimeException('Failed to read distribution_weight');
        }

        $result['distribution_weight'] = $weightData[1];
        $offset += 2;

        // color (uint16, cast to uint8)
        $colorData = unpack('v', substr($binaryData, $offset, 2));
        if ($colorData === false) {
            throw new RuntimeException('Failed to read color');
        }

        $result['color'] = $colorData[1] & 0xFF;
        $offset += 2;

        // supplier_count (uint16)
        $supplierData = unpack('v', substr($binaryData, $offset, 2));
        if ($supplierData === false) {
            throw new RuntimeException('Failed to read supplier_count');
        }

        $result['supplier_count'] = $supplierData[1];
        $offset += 2;

        // product_count (uint16)
        $productData = unpack('v', substr($binaryData, $offset, 2));
        if ($productData === false) {
            throw new RuntimeException('Failed to read product_count');
        }

        $result['product_count'] = $productData[1];

        // Set defaults
        $result['pax_level'] = 12;
        $result['fields'] = 0;

        return $this->buildResult($result);
    }

    /**
     * Parse version 1
     *
     * @return array<string, mixed>
     */
    private function parseVersion1(string $binaryData, int $offset): array
    {
        $result = $this->parseCommonFields($binaryData, $offset, 1);
        $result['fields'] = 0;

        return $this->buildResult($result);
    }

    /**
     * Parse version 2
     *
     * @return array<string, mixed>
     */
    private function parseVersion2(string $binaryData, int $offset): array
    {
        return $this->buildResult($this->parseCommonFields($binaryData, $offset, 2));
    }

    /**
     * Parse version 3
     *
     * @return array<string, mixed>
     */
    private function parseVersion3(string $binaryData, int $offset): array
    {
        return $this->buildResult($this->parseCommonFields($binaryData, $offset, 3));
    }

    /**
     * Parse version 4 (adds sound)
     *
     * @return array<string, mixed>
     */
    private function parseVersion4(string $binaryData, int $offset): array
    {
        $result = $this->parseCommonFields($binaryData, $offset, 4);

        // sound_interval (uint32)
        /** @var int $offset */
        $offset = $result['_offset'];
        $soundIntervalData = unpack('V', substr($binaryData, $offset, 4));
        if ($soundIntervalData === false) {
            throw new RuntimeException('Failed to read sound_interval');
        }

        $result['sound_interval'] = $soundIntervalData[1];
        $offset += 4;

        // sound_id (sint8)
        $soundIdData = unpack('c', substr($binaryData, $offset, 1));
        if ($soundIdData === false) {
            throw new RuntimeException('Failed to read sound_id');
        }

        $result['sound_id'] = $soundIdData[1];

        unset($result['_offset']);

        return $this->buildResult($result);
    }

    /**
     * Parse version 5 (adds smoke offsets)
     *
     * @return array<string, mixed>
     */
    private function parseVersion5(string $binaryData, int $offset): array
    {
        $result = $this->parseCommonFields($binaryData, $offset, 5);

        // sound_interval (uint32)
        /** @var int $offset */
        $offset = $result['_offset'];
        $soundIntervalData = unpack('V', substr($binaryData, $offset, 4));
        if ($soundIntervalData === false) {
            throw new RuntimeException('Failed to read sound_interval');
        }

        $result['sound_interval'] = $soundIntervalData[1];
        $offset += 4;

        // sound_id (sint8)
        $soundIdData = unpack('c', substr($binaryData, $offset, 1));
        if ($soundIdData === false) {
            throw new RuntimeException('Failed to read sound_id');
        }

        $result['sound_id'] = $soundIdData[1];
        $offset += 1;

        // smokerotations (sint8)
        $smokeRotData = unpack('c', substr($binaryData, $offset, 1));
        if ($smokeRotData === false) {
            throw new RuntimeException('Failed to read smokerotations');
        }

        $result['smokerotations'] = $smokeRotData[1];
        $offset += 1;

        // Skip smoke tile and offset data (4 tiles * 8 bytes each = 32 bytes)
        $offset += 32;

        // smokeuplift (uint16)
        $upliftData = unpack('v', substr($binaryData, $offset, 2));
        if ($upliftData === false) {
            throw new RuntimeException('Failed to read smokeuplift');
        }

        $result['smokeuplift'] = $upliftData[1];
        $offset += 2;

        // smokelifetime (uint16)
        $lifetimeData = unpack('v', substr($binaryData, $offset, 2));
        if ($lifetimeData === false) {
            throw new RuntimeException('Failed to read smokelifetime');
        }

        $result['smokelifetime'] = $lifetimeData[1];

        unset($result['_offset']);

        return $this->buildResult($result);
    }

    /**
     * Parse common fields for versions 1-5
     *
     * @return array<string, mixed>
     */
    private function parseCommonFields(string $binaryData, int $offset, int $version): array
    {
        $result = ['version' => $version, '_offset' => $offset];

        // placement (uint16)
        $placementData = unpack('v', substr($binaryData, $offset, 2));
        if ($placementData === false) {
            throw new RuntimeException('Failed to read placement');
        }

        $result['placement'] = $placementData[1];
        $offset += 2;

        // productivity (uint16)
        $productivityData = unpack('v', substr($binaryData, $offset, 2));
        if ($productivityData === false) {
            throw new RuntimeException('Failed to read productivity');
        }

        $result['productivity'] = $productivityData[1];
        $offset += 2;

        // range (uint16)
        $rangeData = unpack('v', substr($binaryData, $offset, 2));
        if ($rangeData === false) {
            throw new RuntimeException('Failed to read range');
        }

        $result['range'] = $rangeData[1];
        $offset += 2;

        // distribution_weight (uint16)
        $weightData = unpack('v', substr($binaryData, $offset, 2));
        if ($weightData === false) {
            throw new RuntimeException('Failed to read distribution_weight');
        }

        $result['distribution_weight'] = $weightData[1];
        $offset += 2;

        // color (uint16 for v1, uint8 for v2+)
        if ($version === 1) {
            $colorData = unpack('v', substr($binaryData, $offset, 2));
            if ($colorData === false) {
                throw new RuntimeException('Failed to read color');
            }

            $result['color'] = $colorData[1] & 0xFF;
            $offset += 2;
        } else {
            $colorData = unpack('C', substr($binaryData, $offset, 1));
            if ($colorData === false) {
                throw new RuntimeException('Failed to read color');
            }

            $result['color'] = $colorData[1];
            $offset += 1;

            // fields (uint8) - only in version 2+
            $fieldsData = unpack('C', substr($binaryData, $offset, 1));
            if ($fieldsData === false) {
                throw new RuntimeException('Failed to read fields');
            }

            $result['fields'] = $fieldsData[1];
            $offset += 1;
        }

        // supplier_count (uint16)
        $supplierData = unpack('v', substr($binaryData, $offset, 2));
        if ($supplierData === false) {
            throw new RuntimeException('Failed to read supplier_count');
        }

        $result['supplier_count'] = $supplierData[1];
        $offset += 2;

        // product_count (uint16)
        $productData = unpack('v', substr($binaryData, $offset, 2));
        if ($productData === false) {
            throw new RuntimeException('Failed to read product_count');
        }

        $result['product_count'] = $productData[1];
        $offset += 2;

        // pax_level (uint16)
        $paxLevelData = unpack('v', substr($binaryData, $offset, 2));
        if ($paxLevelData === false) {
            throw new RuntimeException('Failed to read pax_level');
        }

        $result['pax_level'] = $paxLevelData[1];
        $offset += 2;

        // Version 2+ has expansion parameters
        if ($version >= 2) {
            $result = array_merge($result, $this->parseExpansionFields($binaryData, $offset));
            assert(is_int($result['_offset']));
            $offset = $result['_offset'];
        }

        // Version 3+ has boost and demand parameters
        if ($version >= 3) {
            $result = array_merge($result, $this->parseBoostDemandFields($binaryData, $offset));
            assert(is_int($result['_offset']));
            $offset = $result['_offset'];
        }

        $result['_offset'] = $offset;

        return $result;
    }

    /**
     * Parse expansion fields (version 2+)
     *
     * @return array<string, mixed>
     */
    private function parseExpansionFields(string $binaryData, int $offset): array
    {
        $result = [];

        // Check if we have enough data
        if ($offset + 8 > strlen($binaryData)) {
            // Not enough data for expansion fields, return empty result
            $result['_offset'] = $offset;

            return $result;
        }

        // expand_probability (uint16) - rescaled in source
        $probData = unpack('v', substr($binaryData, $offset, 2));
        if ($probData === false) {
            throw new RuntimeException('Failed to read expand_probability');
        }

        $result['expand_probability'] = $probData[1];
        $offset += 2;

        // expand_minimum (uint16)
        $minData = unpack('v', substr($binaryData, $offset, 2));
        if ($minData === false) {
            throw new RuntimeException('Failed to read expand_minimum');
        }

        $result['expand_minimum'] = $minData[1];
        $offset += 2;

        // expand_range (uint16)
        $rangeData = unpack('v', substr($binaryData, $offset, 2));
        if ($rangeData === false) {
            throw new RuntimeException('Failed to read expand_range');
        }

        $result['expand_range'] = $rangeData[1];
        $offset += 2;

        // expand_times (uint16)
        $timesData = unpack('v', substr($binaryData, $offset, 2));
        if ($timesData === false) {
            throw new RuntimeException('Failed to read expand_times');
        }

        $result['expand_times'] = $timesData[1];
        $offset += 2;

        $result['_offset'] = $offset;

        return $result;
    }

    /**
     * Parse boost and demand fields (version 3+)
     *
     * @return array<string, mixed>
     */
    private function parseBoostDemandFields(string $binaryData, int $offset): array
    {
        $fields = [
            'electric_boost',
            'pax_boost',
            'mail_boost',
            'electric_demand',
            'pax_demand',
            'mail_demand',
        ];

        // Check if we have enough data (6 fields * 2 bytes each = 12 bytes)
        if ($offset + 12 > strlen($binaryData)) {
            // Not enough data for boost/demand fields, return empty result
            return ['_offset' => $offset];
        }

        $result = [];
        foreach ($fields as $field) {
            $data = unpack('v', substr($binaryData, $offset, 2));
            if ($data === false) {
                throw new RuntimeException('Failed to read '.$field);
            }

            $result[$field] = $data[1];
            $offset += 2;
        }

        $result['_offset'] = $offset;

        return $result;
    }

    /**
     * Build final result with human-readable placement name
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function buildResult(array $data): array
    {
        return $data;
    }

    /**
     * Extract input data from FSUP (factory supplier) child nodes
     *
     * @return array<array{good: string, capacity: int, supplier: int, factor: int}>
     */
    private function extractInputFromChildren(Node $node): array
    {
        $inputs = [];

        foreach ($node->getChildren() as $child) {
            $childType = ObjectTypeConverter::toString($child->type);

            if ($childType !== 'fsup') {
                continue;
            }

            // Extract good name from XREF child node
            $goodName = null;
            foreach ($child->getChildren() as $xrefNode) {
                $xrefType = ObjectTypeConverter::toString($xrefNode->type);
                if ($xrefType === 'xref') {
                    $goodName = TextNodeExtractor::extract($xrefNode);
                    if ($goodName !== '' && strlen($goodName) > 5) {
                        $goodName = substr($goodName, 5); // Remove "GOOD:" prefix
                    }

                    break;
                }
            }

            if ($goodName === null) {
                continue;
            }

            // Parse FSUP node data: capacity (uint16), supplier (uint16), factor (uint16)
            $data = $child->data;
            if (strlen($data) >= 6) {
                $values = unpack('vcapacity/vsupplier/vfactor', substr($data, 0, 6));
                if ($values !== false) {
                    $inputs[] = [
                        'good' => $goodName,
                        'capacity' => $values['capacity'],
                        'supplier' => $values['supplier'],
                        'factor' => $values['factor'],
                    ];
                }
            }
        }

        return $inputs;
    }

    /**
     * Extract output data from FPRO (factory product) child nodes
     *
     * @return array<array{good: string, capacity: int, factor: int}>
     */
    private function extractOutputFromChildren(Node $node): array
    {
        $outputs = [];

        foreach ($node->getChildren() as $child) {
            $childType = ObjectTypeConverter::toString($child->type);

            if ($childType !== 'fpro') {
                continue;
            }

            // Extract good name from XREF child node
            $goodName = null;
            foreach ($child->getChildren() as $xrefNode) {
                $xrefType = ObjectTypeConverter::toString($xrefNode->type);
                if ($xrefType === 'xref') {
                    $goodName = TextNodeExtractor::extract($xrefNode);
                    if ($goodName !== '' && strlen($goodName) > 5) {
                        $goodName = substr($goodName, 5); // Remove "GOOD:" prefix
                    }

                    break;
                }
            }

            if ($goodName === null) {
                continue;
            }

            // Parse FPRO node data: capacity (uint16), factor (uint16)
            $data = $child->data;
            if (strlen($data) >= 4) {
                $values = unpack('vcapacity/vfactor', substr($data, 0, 4));
                if ($values !== false) {
                    $outputs[] = [
                        'good' => $goodName,
                        'capacity' => $values['capacity'],
                        'factor' => $values['factor'],
                    ];
                }
            }
        }

        return $outputs;
    }
}
