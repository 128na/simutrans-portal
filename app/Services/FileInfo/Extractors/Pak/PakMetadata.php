<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak;

/**
 * Pak metadata information
 */
final readonly class PakMetadata
{
    /**
     * @param  array<string, mixed>|null  $vehicleData  車両データ（vehicle型の場合のみ）
     */
    private function __construct(
        public string $name,
        public ?string $copyright,
        public string $objectType,
        public int $compilerVersionCode,
        public ?array $vehicleData = null,
    ) {}

    /**
     * @return array{name: string, copyright: string|null, objectType: string, compilerVersionCode: int, vehicleData?: array<string, mixed>}
     */
    public function toArray(): array
    {
        $result = [
            'name' => $this->name,
            'copyright' => $this->copyright,
            'objectType' => $this->objectType,
            'compilerVersionCode' => $this->compilerVersionCode,
        ];

        if ($this->vehicleData !== null) {
            $result['vehicleData'] = $this->vehicleData;
        }

        return $result;
    }

    public static function fromNode(Node $node, int $versionCode): self
    {
        // Child node 0 is name (TEXT node)
        $nameNode = $node->getChild(0);
        $name = '';
        if ($nameNode instanceof \App\Services\FileInfo\Extractors\Pak\Node && $nameNode->isType(Node::OBJ_TEXT)) {
            $name = self::extractTextFromNode($nameNode);
        }

        // Child node 1 is copyright (TEXT node)
        $copyrightNode = $node->getChild(1);
        $copyright = null;
        if ($copyrightNode instanceof \App\Services\FileInfo\Extractors\Pak\Node && $copyrightNode->isType(Node::OBJ_TEXT)) {
            $copyright = self::extractTextFromNode($copyrightNode);
            if ($copyright === '') {
                $copyright = null;
            }
        }

        // Determine object type from parent node
        $objectType = ObjectTypeConverter::toString($node->type);

        // Parse vehicle-specific data if this is a vehicle node
        $vehicleData = null;
        if ($node->isType(Node::OBJ_VEHICLE)) {
            $vehicleData = self::parseVehicleData($node);
        }

        return new self($name, $copyright, $objectType, $versionCode, $vehicleData);
    }

    private static function extractTextFromNode(Node $node): string
    {
        // TEXT node data is a null-terminated string
        $nullPos = strpos($node->data, "\0");
        if ($nullPos === false) {
            return $node->data;
        }

        return substr($node->data, 0, $nullPos);
    }

    /**
     * 車両データをパース
     *
     * 参考: simutrans/descriptor/reader/vehicle_reader.cc
     *
     * @return array<string, mixed>|null
     */
    private static function parseVehicleData(Node $node): ?array
    {
        if (strlen($node->data) < 2) {
            return null;
        }

        $reader = new BinaryReader($node->data);

        try {
            // Read version stamp
            $v = $reader->readUint16LE();
            $version = ($v & 0x8000) ? ($v & 0x7FFF) : 0;

            $data = [];

            // Parse based on version
            if ($version === 0) {
                // old node, version 0
                // wtyp is already read as $v
                $data['wtyp'] = $v;
                $data['capacity'] = $reader->readUint16LE();
                $data['price'] = $reader->readUint32LE();
                $data['topspeed'] = $reader->readUint16LE();
                $data['weight'] = $reader->readUint16LE() * 1000; // old weights were tons
                $data['power'] = $reader->readUint16LE();
                $data['running_cost'] = $reader->readUint16LE();
            } elseif ($version === 1) {
                $data['price'] = $reader->readUint32LE();
                $data['capacity'] = $reader->readUint16LE();
                $data['topspeed'] = $reader->readUint16LE();
                $data['weight'] = $reader->readUint16LE() * 1000;
                $data['power'] = $reader->readUint16LE();
                $data['running_cost'] = $reader->readUint16LE();
                $data['intro_date'] = $reader->readUint16LE();
                $data['gear'] = $reader->readUint8();
                $data['wtyp'] = $reader->readUint8();
                $data['sound'] = $reader->readUint8();
                $data['leader_count'] = $reader->readUint8();
                $data['trailer_count'] = $reader->readUint8();
            } elseif ($version === 2) {
                $data['price'] = $reader->readUint32LE();
                $data['capacity'] = $reader->readUint16LE();
                $data['topspeed'] = $reader->readUint16LE();
                $data['weight'] = $reader->readUint16LE() * 1000;
                $data['power'] = $reader->readUint16LE();
                $data['running_cost'] = $reader->readUint16LE();
                $data['intro_date'] = $reader->readUint16LE();
                $data['gear'] = $reader->readUint8();
                $data['wtyp'] = $reader->readUint8();
                $data['sound'] = $reader->readUint8();
                $data['leader_count'] = $reader->readUint8();
                $data['trailer_count'] = $reader->readUint8();
                $data['engine_type'] = $reader->readUint8();
            } elseif (in_array($version, [3, 4, 5], true)) {
                $data['price'] = $reader->readUint32LE();
                $data['capacity'] = $reader->readUint16LE();
                $data['topspeed'] = $reader->readUint16LE();
                $data['weight'] = $reader->readUint16LE() * 1000;
                $data['power'] = $reader->readUint16LE();
                $data['running_cost'] = $reader->readUint16LE();
                $data['intro_date'] = $reader->readUint16LE();
                $data['retire_date'] = $reader->readUint16LE();
                $data['gear'] = $reader->readUint8();
                $data['wtyp'] = $reader->readUint8();
                $data['sound'] = $reader->readUint8();
                $data['leader_count'] = $reader->readUint8();
                $data['trailer_count'] = $reader->readUint8();
                $data['engine_type'] = $reader->readUint8();
            } elseif ($version === 6) {
                $data['price'] = $reader->readUint32LE();
                $data['capacity'] = $reader->readUint16LE();
                $data['topspeed'] = $reader->readUint16LE();
                $data['weight'] = $reader->readUint16LE() * 1000;
                $data['power'] = $reader->readUint32LE();
                $data['running_cost'] = $reader->readUint16LE();
                $data['intro_date'] = $reader->readUint16LE();
                $data['retire_date'] = $reader->readUint16LE();
                $data['gear'] = $reader->readUint16LE();
                $data['wtyp'] = $reader->readUint8();
                $data['sound'] = $reader->readUint8();
                $data['engine_type'] = $reader->readUint8();
                $data['leader_count'] = $reader->readUint8();
                $data['trailer_count'] = $reader->readUint8();
            } elseif ($version === 7) {
                $data['price'] = $reader->readUint32LE();
                $data['capacity'] = $reader->readUint16LE();
                $data['topspeed'] = $reader->readUint16LE();
                $data['weight'] = $reader->readUint16LE() * 1000;
                $data['power'] = $reader->readUint32LE();
                $data['running_cost'] = $reader->readUint16LE();
                $data['intro_date'] = $reader->readUint16LE();
                $data['retire_date'] = $reader->readUint16LE();
                $data['gear'] = $reader->readUint16LE();
                $data['wtyp'] = $reader->readUint8();
                $data['sound'] = $reader->readUint8();
                $data['engine_type'] = $reader->readUint8();
                $data['len'] = $reader->readUint8();
                $data['leader_count'] = $reader->readUint8();
                $data['trailer_count'] = $reader->readUint8();
            } elseif ($version === 8) {
                $data['price'] = $reader->readUint32LE();
                $data['capacity'] = $reader->readUint16LE();
                $data['topspeed'] = $reader->readUint16LE();
                $data['weight'] = $reader->readUint16LE() * 1000;
                $data['power'] = $reader->readUint32LE();
                $data['running_cost'] = $reader->readUint16LE();
                $data['intro_date'] = $reader->readUint16LE();
                $data['retire_date'] = $reader->readUint16LE();
                $data['gear'] = $reader->readUint16LE();
                $data['wtyp'] = $reader->readUint8();
                $data['sound'] = $reader->readUint8();
                $data['engine_type'] = $reader->readUint8();
                $data['len'] = $reader->readUint8();
                $data['leader_count'] = $reader->readUint8();
                $data['trailer_count'] = $reader->readUint8();
                $data['freight_image_type'] = $reader->readUint8();
            } elseif ($version === 9) {
                $data['price'] = $reader->readUint32LE();
                $data['capacity'] = $reader->readUint16LE();
                $data['loading_time'] = $reader->readUint16LE();
                $data['topspeed'] = $reader->readUint16LE();
                $data['weight'] = $reader->readUint16LE() * 1000;
                $data['axle_load'] = $reader->readUint16LE();
                $data['power'] = $reader->readUint32LE();
                $data['running_cost'] = $reader->readUint16LE();
                $data['maintenance'] = $reader->readUint16LE();
                $data['intro_date'] = $reader->readUint16LE();
                $data['retire_date'] = $reader->readUint16LE();
                $data['gear'] = $reader->readUint16LE();
                $data['wtyp'] = $reader->readUint8();
                $data['sound'] = $reader->readUint8();
                $data['engine_type'] = $reader->readUint8();
                $data['len'] = $reader->readUint8();
                $data['leader_count'] = $reader->readUint8();
                $data['trailer_count'] = $reader->readUint8();
                $data['freight_image_type'] = $reader->readUint8();
            } elseif ($version === 10) {
                $data['price'] = $reader->readUint32LE();
                $data['capacity'] = $reader->readUint16LE();
                $data['loading_time'] = $reader->readUint16LE();
                $data['topspeed'] = $reader->readUint16LE();
                $data['weight'] = $reader->readUint32LE(); // weight in kgs
                $data['axle_load'] = $reader->readUint16LE();
                $data['power'] = $reader->readUint32LE();
                $data['running_cost'] = $reader->readUint16LE();
                $data['maintenance'] = $reader->readUint16LE();
                $data['intro_date'] = $reader->readUint16LE();
                $data['retire_date'] = $reader->readUint16LE();
                $data['gear'] = $reader->readUint16LE();
                $data['wtyp'] = $reader->readUint8();
                $data['sound'] = $reader->readUint8();
                $data['engine_type'] = $reader->readUint8();
                $data['len'] = $reader->readUint8();
                $data['leader_count'] = $reader->readUint8();
                $data['trailer_count'] = $reader->readUint8();
                $data['freight_image_type'] = $reader->readUint8();
            } elseif ($version === 11) {
                $data['price'] = $reader->readUint32LE();
                $data['capacity'] = $reader->readUint16LE();
                $data['loading_time'] = $reader->readUint16LE();
                $data['topspeed'] = $reader->readUint16LE();
                $data['weight'] = $reader->readUint32LE();
                $data['axle_load'] = $reader->readUint16LE();
                $data['power'] = $reader->readUint32LE();
                $data['running_cost'] = $reader->readUint16LE();
                $data['maintenance'] = $reader->readUint32LE();
                $data['intro_date'] = $reader->readUint16LE();
                $data['retire_date'] = $reader->readUint16LE();
                $data['gear'] = $reader->readUint16LE();
                $data['wtyp'] = $reader->readUint8();
                $data['sound'] = $reader->readUint8();
                $data['engine_type'] = $reader->readUint8();
                $data['len'] = $reader->readUint8();
                $data['leader_count'] = $reader->readUint8();
                $data['trailer_count'] = $reader->readUint8();
                $data['freight_image_type'] = $reader->readUint8();
            } elseif ($version === 12) {
                // Cost values as sint64 (but we'll read as uint for simplicity)
                $data['price'] = $reader->readUint32LE(); // lower 32 bits
                $reader->readUint32LE(); // upper 32 bits (ignored for now)
                $data['capacity'] = $reader->readUint16LE();
                $data['loading_time'] = $reader->readUint16LE();
                $data['topspeed'] = $reader->readUint16LE();
                $data['weight'] = $reader->readUint32LE();
                $data['axle_load'] = $reader->readUint16LE();
                $data['power'] = $reader->readUint32LE();
                $data['running_cost'] = $reader->readUint32LE(); // lower 32 bits
                $reader->readUint32LE(); // upper 32 bits
                $data['maintenance'] = $reader->readUint32LE(); // lower 32 bits
                $reader->readUint32LE(); // upper 32 bits
                $data['intro_date'] = $reader->readUint16LE();
                $data['retire_date'] = $reader->readUint16LE();
                $data['gear'] = $reader->readUint16LE();
                $data['wtyp'] = $reader->readUint8();
                $data['sound'] = $reader->readUint8();
                $data['engine_type'] = $reader->readUint8();
                $data['len'] = $reader->readUint8();
                $data['leader_count'] = $reader->readUint8();
                $data['trailer_count'] = $reader->readUint8();
                $data['freight_image_type'] = $reader->readUint8();
            }

            // Convert engine_type to string if present
            if (isset($data['engine_type'])) {
                $data['engine_type_str'] = EngineTypeConverter::convert($data['engine_type']);
            }

            // Get freight type from child node 2 if exists
            // It can be either a TEXT node or an XREF node
            $freightNode = $node->getChild(2);
            if ($freightNode instanceof Node) {
                if ($freightNode->isType(Node::OBJ_TEXT)) {
                    // Direct TEXT node
                    $data['freight_type'] = self::extractTextFromNode($freightNode);
                } elseif ($freightNode->isType(Node::OBJ_XREF)) {
                    // XREF node format: 4-char type + separator byte + null-terminated name
                    // e.g., 'GOOD\x01goods\x00'
                    $xrefText = self::extractTextFromNode($freightNode);
                    // Remove the 4-character type prefix and separator (5 bytes total)
                    if (strlen($xrefText) > 5) {
                        $data['freight_type'] = substr($xrefText, 5);
                    }
                } else {
                    // Try child node 0 for nested structure
                    $freightNameNode = $freightNode->getChild(0);
                    if ($freightNameNode instanceof Node && $freightNameNode->isType(Node::OBJ_TEXT)) {
                        $data['freight_type'] = self::extractTextFromNode($freightNameNode);
                    }
                }
            }

            return $data;
        } catch (\Throwable) {
            return null;
        }
    }
}
