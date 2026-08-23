<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Enums\PakObjectType;
use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\ObjectTypeConverter;
use App\Services\FileInfo\Extractors\Pak\TextNodeExtractor;
use App\Services\FileInfo\Extractors\Pak\VersionStamp;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Parser for factory (industrial building) nodes
 *
 * Factories produce and consume goods. They are the economic heart of Simutrans.
 * Supported versions: 0-6
 *
 * Child nodes: FSUP (supplier), FPRO (product), FFIE (field group, itself
 * containing FFCL field-class children), FSMO (smoke position).
 *
 * Note: the FFIE/FFCL/FSMO parsing (field-node nesting, version-stamp values)
 * is verified against the reader source below but not against a real
 * field-producing pak binary (e.g. an oil derrick) - none of this repo's
 * test fixtures contain field or smoke data. If a factory with known fields
 * shows an empty field list, re-check this against a real sample.
 *
 * @see https://github.com/aburch/simutrans/blob/master/src/simutrans/descriptor/reader/factory_reader.cc
 */
class FactoryParser implements TypeParserInterface
{
    private const int MAX_SUPPORTED_VERSION = 6;

    private const int MAX_SUPPORTED_FIELD_GROUP_VERSION = 3;

    private const int MAX_SUPPORTED_FIELD_CLASS_VERSION = 1;

    public function canParse(Node $node): bool
    {
        return $node->type === Node::OBJ_FACTORY;
    }

    /**
     * @return array<string, mixed>
     */
    public function parse(Node $node): array
    {
        $binaryData = $node->data;
        $offset = 0;

        $stamp = VersionStamp::from($binaryData, $offset);
        $offset += 2;

        if ($stamp->isVersioned) {
            $result = match ($stamp->version) {
                1 => $this->parseVersion1($binaryData, $offset),
                2 => $this->parseVersion2($binaryData, $offset),
                3 => $this->parseVersion3($binaryData, $offset),
                4 => $this->parseVersion4($binaryData, $offset),
                5 => $this->parseVersion5($binaryData, $offset),
                6 => $this->parseVersion6($binaryData, $offset),
                default => throw InvalidPakFileException::unsupportedTypeVersion('factory', $stamp->version, self::MAX_SUPPORTED_VERSION),
            };
        } else {
            // Version 0 (legacy): firstUint16 is placement type
            $result = $this->parseVersion0($binaryData, $offset, $stamp->firstUint16);
        }

        // Extract input data from FSUP (factory supplier) child nodes
        $result['input'] = $this->extractInputFromChildren($node);

        // Extract output data from FPRO (factory product) child nodes
        $result['output'] = $this->extractOutputFromChildren($node);

        // Extract field group data from FFIE (factory field) child nodes.
        // Note: 'fields' (from parseCommonFields) is the raw field-slot count;
        // 'field_groups' holds the parsed FFIE/FFCL structures.
        //
        // FFIE/FFCL are versioned independently of the main FACT version, so an
        // unsupported field-group/class version is caught here rather than left
        // to propagate out of parse() - otherwise it would wipe out the whole
        // factory's data (productivity, price, input, output, ...) instead of
        // just the field section.
        try {
            $result['field_groups'] = $this->extractFieldsFromChildren($node);
        } catch (InvalidPakFileException $exception) {
            Log::warning('Skipping unsupported factory field group', [
                'exception' => $exception->getMessage(),
            ]);
            $result['field_groups'] = [];
        }

        // Extract smoke position data from FSMO (factory smoke) child nodes
        $result['smoke'] = $this->extractSmokeFromChildren($node);

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
     * Parse version 6 (adds explicit electricity producer flag)
     *
     * @return array<string, mixed>
     */
    private function parseVersion6(string $binaryData, int $offset): array
    {
        $result = $this->parseCommonFields($binaryData, $offset, 6);

        /** @var int $offset */
        $offset = $result['_offset'];

        // electricity_producer (uint8) - NEW in version 6, read before sound_interval
        $producerData = unpack('C', substr($binaryData, $offset, 1));
        if ($producerData === false) {
            throw new RuntimeException('Failed to read electricity_producer');
        }

        $result['electricity_producer'] = $producerData[1];
        $offset += 1;

        // sound_interval (uint32)
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
     * Parse common fields for versions 1-6
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

        // expand_probability (uint16), rescaled via rescale_probability() in source
        $probData = unpack('v', substr($binaryData, $offset, 2));
        if ($probData === false) {
            throw new RuntimeException('Failed to read expand_probability');
        }

        $result['expand_probability'] = $this->rescaleProbability($probData[1]);
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
     * Combine 256 independent rounds of a p/10000 chance into a single probability.
     *
     * @see https://github.com/aburch/simutrans/blob/master/src/simutrans/descriptor/reader/factory_reader.cc rescale_probability()
     */
    private function rescaleProbability(int $p): int
    {
        if ($p === 0) {
            return 0;
        }

        if ($p >= 10000) {
            return 10000;
        }

        $pp = intdiv($p << 30, 10000);
        $qq = (1 << 30) - $pp;

        $ss = 256;
        while (($ss >>= 1) !== 0) {
            $pp += ($pp * $qq) >> 30;
            $qq = ($qq * $qq) >> 30;
        }

        return (($pp * 10000) + (1 << 29)) >> 30;
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
            $childType = ObjectTypeConverter::toEnum($child->type);

            if ($childType !== PakObjectType::FactorySupplier) {
                continue;
            }

            // Extract good name from XREF child node
            $goodName = null;
            foreach ($child->getChildren() as $xrefNode) {
                $xrefType = ObjectTypeConverter::toEnum($xrefNode->type);
                if ($xrefType === PakObjectType::Xref) {
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
            $childType = ObjectTypeConverter::toEnum($child->type);

            if ($childType !== PakObjectType::FactoryProduct) {
                continue;
            }

            // Extract good name from XREF child node
            $goodName = null;
            foreach ($child->getChildren() as $xrefNode) {
                $xrefType = ObjectTypeConverter::toEnum($xrefNode->type);
                if ($xrefType === PakObjectType::Xref) {
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

    /**
     * Extract field group data from FFIE (factory field) child nodes
     *
     * @return array<array{
     *     version: int,
     *     probability: int,
     *     max_fields: int,
     *     min_fields: int,
     *     start_fields: ?int,
     *     classes: array<array{snow_image: int, production_per_field: int, storage_capacity: int, spawn_weight: int}>
     * }>
     */
    private function extractFieldsFromChildren(Node $node): array
    {
        $groups = [];

        foreach ($node->getChildren() as $child) {
            if (ObjectTypeConverter::toEnum($child->type) === PakObjectType::FactoryFieldGroup) {
                $groups[] = $this->parseFieldGroup($child);
            }
        }

        return $groups;
    }

    /**
     * Parse a single FFIE (field group) node
     *
     * @return array{
     *     version: int,
     *     probability: int,
     *     max_fields: int,
     *     min_fields: int,
     *     start_fields: ?int,
     *     classes: array<array{snow_image: int, production_per_field: int, storage_capacity: int, spawn_weight: int}>
     * }
     */
    private function parseFieldGroup(Node $node): array
    {
        $reader = new BinaryReader($node->data);
        $version = $reader->readUint16LE() & 0x7FFF;

        if ($version === 1) {
            // v1: class data is embedded directly in the group node (single implicit class)
            $snowImage = $reader->readUint8();
            $probability = $this->rescaleProbability($reader->readUint16LE());
            $productionPerField = $reader->readUint16LE();

            return [
                'version' => 1,
                'probability' => $probability,
                'max_fields' => $reader->readUint16LE(),
                'min_fields' => $reader->readUint16LE(),
                'start_fields' => null,
                'classes' => [[
                    'snow_image' => $snowImage,
                    'production_per_field' => $productionPerField,
                    'storage_capacity' => 0,
                    'spawn_weight' => 1000,
                ]],
            ];
        }

        if ($version === 2 || $version === 3) {
            $probability = $this->rescaleProbability($reader->readUint16LE());
            $maxFields = $reader->readUint16LE();
            $minFields = $reader->readUint16LE();
            $startFields = $version === 3 ? $reader->readUint16LE() : null;
            $reader->readUint16LE(); // field_classes count (子ノードを直接数えるため未使用)

            return [
                'version' => $version,
                'probability' => $probability,
                'max_fields' => $maxFields,
                'min_fields' => $minFields,
                'start_fields' => $startFields,
                'classes' => $this->extractFieldClassesFromChildren($node),
            ];
        }

        throw InvalidPakFileException::unsupportedTypeVersion('ffield', $version, self::MAX_SUPPORTED_FIELD_GROUP_VERSION);
    }

    /**
     * Extract field class data from FFCL (field class) child nodes of a field group
     *
     * @return array<array{snow_image: int, production_per_field: int, storage_capacity: int, spawn_weight: int}>
     */
    private function extractFieldClassesFromChildren(Node $node): array
    {
        $classes = [];

        foreach ($node->getChildren() as $child) {
            if (ObjectTypeConverter::toEnum($child->type) === PakObjectType::FactoryFieldClass) {
                $classes[] = $this->parseFieldClass($child);
            }
        }

        return $classes;
    }

    /**
     * Parse a single FFCL (field class) node
     *
     * @return array{snow_image: int, production_per_field: int, storage_capacity: int, spawn_weight: int}
     */
    private function parseFieldClass(Node $node): array
    {
        $reader = new BinaryReader($node->data);
        $version = $reader->readUint16LE() & 0x7FFF;

        if ($version !== 1) {
            throw InvalidPakFileException::unsupportedTypeVersion('ffldclass', $version, self::MAX_SUPPORTED_FIELD_CLASS_VERSION);
        }

        return [
            'snow_image' => $reader->readUint8(),
            'production_per_field' => $reader->readUint16LE(),
            'storage_capacity' => $reader->readUint16LE(),
            'spawn_weight' => $reader->readUint16LE(),
        ];
    }

    /**
     * Extract smoke position data from FSMO (factory smoke) child nodes
     *
     * @return array<array{pos_x: int, pos_y: int, offset_x: int, offset_y: int}>
     */
    private function extractSmokeFromChildren(Node $node): array
    {
        $smoke = [];

        foreach ($node->getChildren() as $child) {
            if (ObjectTypeConverter::toEnum($child->type) === PakObjectType::FactorySmoke) {
                $smoke[] = $this->parseSmoke($child);
            }
        }

        return $smoke;
    }

    /**
     * Parse a single FSMO (smoke) node. Has no version stamp (factory_smoke_reader.cc).
     *
     * @return array{pos_x: int, pos_y: int, offset_x: int, offset_y: int}
     */
    private function parseSmoke(Node $node): array
    {
        $reader = new BinaryReader($node->data);

        return [
            'pos_x' => $reader->readSint16LE(),
            'pos_y' => $reader->readSint16LE(),
            'offset_x' => $reader->readSint16LE(),
            'offset_y' => $reader->readSint16LE(),
        ];
    }
}
