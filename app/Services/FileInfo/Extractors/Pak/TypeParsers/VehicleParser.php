<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\TextNodeExtractor;

/**
 * Vehicle data parser
 *
 * 参考: simutrans/descriptor/reader/vehicle_reader.cc
 */
final readonly class VehicleParser implements TypeParserInterface
{
    #[\Override]
    public function canParse(Node $node): bool
    {
        return $node->isType(Node::OBJ_VEHICLE);
    }

    #[\Override]
    public function parse(Node $node): ?array
    {
        if (strlen($node->data) < 2) {
            return null;
        }

        $reader = new BinaryReader($node->data);

        try {
            // Read version stamp
            $v = $reader->readUint16LE();
            $version = (($v & 0x8000) !== 0) ? ($v & 0x7FFF) : 0;

            $data = match ($version) {
                0 => $this->parseVersion0($reader, $v),
                1, 2 => $this->parseVersion1And2($reader, $version),
                3, 4, 5 => $this->parseVersion3To5($reader),
                6 => $this->parseVersion6($reader),
                7 => $this->parseVersion7($reader),
                8 => $this->parseVersion8($reader),
                9 => $this->parseVersion9($reader),
                10 => $this->parseVersion10($reader),
                11 => $this->parseVersion11($reader),
                12 => $this->parseVersion12($reader),
                default => [],
            };

            if (isset($data['engine_type'])) {
                assert(is_int($data['engine_type']));
            }

            // Get freight type from child node 2 if exists
            $freightType = $this->extractFreightType($node);
            if ($freightType !== null) {
                $data['freight_type'] = $freightType;
            }

            return $data;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion0(BinaryReader $reader, int $v): array
    {
        return [
            'waytype' => $v,
            'capacity' => $reader->readUint16LE(),
            'price' => $reader->readUint32LE(),
            'topspeed' => $reader->readUint16LE(),
            'weight' => $reader->readUint16LE() * 1000,
            'power' => $reader->readUint16LE(),
            'running_cost' => $reader->readUint16LE(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion1And2(BinaryReader $reader, int $version): array
    {
        $data = [
            'price' => $reader->readUint32LE(),
            'capacity' => $reader->readUint16LE(),
            'topspeed' => $reader->readUint16LE(),
            'weight' => $reader->readUint16LE() * 1000,
            'power' => $reader->readUint16LE(),
            'running_cost' => $reader->readUint16LE(),
            'intro_date' => $reader->readUint16LE(),
            'gear' => $reader->readUint8(),
            'waytype' => $reader->readUint8(),
            'sound' => $reader->readUint8(),
            'leader_count' => $reader->readUint8(),
            'trailer_count' => $reader->readUint8(),
        ];

        if ($version === 2) {
            $data['engine_type'] = $reader->readUint8();
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion3To5(BinaryReader $reader): array
    {
        return [
            'price' => $reader->readUint32LE(),
            'capacity' => $reader->readUint16LE(),
            'topspeed' => $reader->readUint16LE(),
            'weight' => $reader->readUint16LE() * 1000,
            'power' => $reader->readUint16LE(),
            'running_cost' => $reader->readUint16LE(),
            'intro_date' => $reader->readUint16LE(),
            'retire_date' => $reader->readUint16LE(),
            'gear' => $reader->readUint8(),
            'waytype' => $reader->readUint8(),
            'sound' => $reader->readUint8(),
            'leader_count' => $reader->readUint8(),
            'trailer_count' => $reader->readUint8(),
            'engine_type' => $reader->readUint8(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion6(BinaryReader $reader): array
    {
        return [
            'price' => $reader->readUint32LE(),
            'capacity' => $reader->readUint16LE(),
            'topspeed' => $reader->readUint16LE(),
            'weight' => $reader->readUint16LE() * 1000,
            'power' => $reader->readUint32LE(),
            'running_cost' => $reader->readUint16LE(),
            'intro_date' => $reader->readUint16LE(),
            'retire_date' => $reader->readUint16LE(),
            'gear' => $reader->readUint16LE(),
            'waytype' => $reader->readUint8(),
            'sound' => $reader->readUint8(),
            'engine_type' => $reader->readUint8(),
            'leader_count' => $reader->readUint8(),
            'trailer_count' => $reader->readUint8(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion7(BinaryReader $reader): array
    {
        return [
            'price' => $reader->readUint32LE(),
            'capacity' => $reader->readUint16LE(),
            'topspeed' => $reader->readUint16LE(),
            'weight' => $reader->readUint16LE() * 1000,
            'power' => $reader->readUint32LE(),
            'running_cost' => $reader->readUint16LE(),
            'intro_date' => $reader->readUint16LE(),
            'retire_date' => $reader->readUint16LE(),
            'gear' => $reader->readUint16LE(),
            'waytype' => $reader->readUint8(),
            'sound' => $reader->readUint8(),
            'engine_type' => $reader->readUint8(),
            'len' => $reader->readUint8(),
            'leader_count' => $reader->readUint8(),
            'trailer_count' => $reader->readUint8(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion8(BinaryReader $reader): array
    {
        return [
            'price' => $reader->readUint32LE(),
            'capacity' => $reader->readUint16LE(),
            'topspeed' => $reader->readUint16LE(),
            'weight' => $reader->readUint16LE() * 1000,
            'power' => $reader->readUint32LE(),
            'running_cost' => $reader->readUint16LE(),
            'intro_date' => $reader->readUint16LE(),
            'retire_date' => $reader->readUint16LE(),
            'gear' => $reader->readUint16LE(),
            'waytype' => $reader->readUint8(),
            'sound' => $reader->readUint8(),
            'engine_type' => $reader->readUint8(),
            'len' => $reader->readUint8(),
            'leader_count' => $reader->readUint8(),
            'trailer_count' => $reader->readUint8(),
            'freight_image_type' => $reader->readUint8(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion9(BinaryReader $reader): array
    {
        return [
            'price' => $reader->readUint32LE(),
            'capacity' => $reader->readUint16LE(),
            'loading_time' => $reader->readUint16LE(),
            'topspeed' => $reader->readUint16LE(),
            'weight' => $reader->readUint16LE() * 1000,
            'axle_load' => $reader->readUint16LE(),
            'power' => $reader->readUint32LE(),
            'running_cost' => $reader->readUint16LE(),
            'maintenance' => $reader->readUint16LE(),
            'intro_date' => $reader->readUint16LE(),
            'retire_date' => $reader->readUint16LE(),
            'gear' => $reader->readUint16LE(),
            'waytype' => $reader->readUint8(),
            'sound' => $reader->readUint8(),
            'engine_type' => $reader->readUint8(),
            'len' => $reader->readUint8(),
            'leader_count' => $reader->readUint8(),
            'trailer_count' => $reader->readUint8(),
            'freight_image_type' => $reader->readUint8(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion10(BinaryReader $reader): array
    {
        return [
            'price' => $reader->readUint32LE(),
            'capacity' => $reader->readUint16LE(),
            'loading_time' => $reader->readUint16LE(),
            'topspeed' => $reader->readUint16LE(),
            'weight' => $reader->readUint32LE(),
            'axle_load' => $reader->readUint16LE(),
            'power' => $reader->readUint32LE(),
            'running_cost' => $reader->readUint16LE(),
            'maintenance' => $reader->readUint16LE(),
            'intro_date' => $reader->readUint16LE(),
            'retire_date' => $reader->readUint16LE(),
            'gear' => $reader->readUint16LE(),
            'waytype' => $reader->readUint8(),
            'sound' => $reader->readUint8(),
            'engine_type' => $reader->readUint8(),
            'len' => $reader->readUint8(),
            'leader_count' => $reader->readUint8(),
            'trailer_count' => $reader->readUint8(),
            'freight_image_type' => $reader->readUint8(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion11(BinaryReader $reader): array
    {
        return [
            'price' => $reader->readUint32LE(),
            'capacity' => $reader->readUint16LE(),
            'loading_time' => $reader->readUint16LE(),
            'topspeed' => $reader->readUint16LE(),
            'weight' => $reader->readUint32LE(),
            'axle_load' => $reader->readUint16LE(),
            'power' => $reader->readUint32LE(),
            'running_cost' => $reader->readUint16LE(),
            'maintenance' => $reader->readUint32LE(),
            'intro_date' => $reader->readUint16LE(),
            'retire_date' => $reader->readUint16LE(),
            'gear' => $reader->readUint16LE(),
            'waytype' => $reader->readUint8(),
            'sound' => $reader->readUint8(),
            'engine_type' => $reader->readUint8(),
            'len' => $reader->readUint8(),
            'leader_count' => $reader->readUint8(),
            'trailer_count' => $reader->readUint8(),
            'freight_image_type' => $reader->readUint8(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion12(BinaryReader $reader): array
    {
        $price = $reader->readUint32LE();
        $reader->readUint32LE(); // upper 32 bits
        $capacity = $reader->readUint16LE();
        $loadingTime = $reader->readUint16LE();
        $topspeed = $reader->readUint16LE();
        $weight = $reader->readUint32LE();
        $axleLoad = $reader->readUint16LE();
        $power = $reader->readUint32LE();
        $runningCost = $reader->readUint32LE();
        $reader->readUint32LE(); // upper 32 bits
        $maintenance = $reader->readUint32LE();
        $reader->readUint32LE(); // upper 32 bits

        return [
            'price' => $price,
            'capacity' => $capacity,
            'loading_time' => $loadingTime,
            'topspeed' => $topspeed,
            'weight' => $weight,
            'axle_load' => $axleLoad,
            'power' => $power,
            'running_cost' => $runningCost,
            'maintenance' => $maintenance,
            'intro_date' => $reader->readUint16LE(),
            'retire_date' => $reader->readUint16LE(),
            'gear' => $reader->readUint16LE(),
            'waytype' => $reader->readUint8(),
            'sound' => $reader->readUint8(),
            'engine_type' => $reader->readUint8(),
            'len' => $reader->readUint8(),
            'leader_count' => $reader->readUint8(),
            'trailer_count' => $reader->readUint8(),
            'freight_image_type' => $reader->readUint8(),
        ];
    }

    private function extractFreightType(Node $node): ?string
    {
        $freightNode = $node->getChild(2);
        if (! $freightNode instanceof Node) {
            return null;
        }

        if ($freightNode->isType(Node::OBJ_TEXT)) {
            // Direct TEXT node
            return TextNodeExtractor::extract($freightNode);
        }

        if ($freightNode->isType(Node::OBJ_XREF)) {
            // XREF node format: 4-char type + separator byte + null-terminated name
            $xrefText = TextNodeExtractor::extract($freightNode);
            if (strlen($xrefText) > 5) {
                return substr($xrefText, 5);
            }
        } else {
            // Try child node 0 for nested structure
            $freightNameNode = $freightNode->getChild(0);
            if ($freightNameNode instanceof Node && $freightNameNode->isType(Node::OBJ_TEXT)) {
                return TextNodeExtractor::extract($freightNameNode);
            }
        }

        return null;
    }
}
