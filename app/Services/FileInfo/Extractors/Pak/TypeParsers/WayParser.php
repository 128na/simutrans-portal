<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\WayTypeConverter;

/**
 * Way data parser
 *
 * 参考: simutrans/descriptor/reader/way_reader.cc
 */
final readonly class WayParser implements TypeParserInterface
{
    #[\Override]
    public function canParse(Node $node): bool
    {
        return $node->isType(Node::OBJ_WAY);
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
            $version = $v & 0x7FFF;

            $data = match ($version) {
                0 => $this->parseVersion0(),
                1 => $this->parseVersion1($reader),
                2 => $this->parseVersion2($reader),
                3 => $this->parseVersion3($reader),
                4, 5 => $this->parseVersion4And5($reader),
                6 => $this->parseVersion6($reader),
                7 => $this->parseVersion7($reader),
                default => [],
            };

            // Apply internal corrections (from way_reader.cc)
            $this->applyInternalCorrections($data);

            // Convert wtyp to string
            if (isset($data['wtyp'])) {
                assert(is_int($data['wtyp']));
                $data['wtyp_str'] = WayTypeConverter::getWayTypeName($data['wtyp']);
            }

            // Convert styp to string
            if (isset($data['styp'])) {
                assert(is_int($data['styp']));
                $data['styp_str'] = WayTypeConverter::getSystemTypeName($data['styp']);
            }

            return $data;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion0(): array
    {
        return [
            'price' => 10000,
            'maintenance' => 800,
            'topspeed' => 999,
            'max_weight' => 999,
            'intro_date' => 1930 * 12,
            'retire_date' => 2999 * 12,
            'wtyp' => 1,
            'styp' => 0,
            'draw_as_obj' => false,
            'number_of_seasons' => 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion1(BinaryReader $reader): array
    {
        $introDateRaw = $reader->readUint32LE();

        return [
            'price' => $reader->readUint32LE(),
            'maintenance' => $reader->readUint32LE(),
            'topspeed' => $reader->readUint32LE(),
            'max_weight' => $reader->readUint32LE(),
            'intro_date' => intdiv($introDateRaw, 16) * 12 + ($introDateRaw % 16),
            'wtyp' => $reader->readUint8(),
            'styp' => $reader->readUint8(),
            'retire_date' => 2999 * 12,
            'draw_as_obj' => false,
            'number_of_seasons' => 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion2(BinaryReader $reader): array
    {
        return [
            'price' => $reader->readUint32LE(),
            'maintenance' => $reader->readUint32LE(),
            'topspeed' => $reader->readUint32LE(),
            'max_weight' => $reader->readUint32LE(),
            'intro_date' => $reader->readUint16LE(),
            'retire_date' => $reader->readUint16LE(),
            'wtyp' => $reader->readUint8(),
            'styp' => $reader->readUint8(),
            'draw_as_obj' => false,
            'number_of_seasons' => 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion3(BinaryReader $reader): array
    {
        return [
            'price' => $reader->readUint32LE(),
            'maintenance' => $reader->readUint32LE(),
            'topspeed' => $reader->readUint32LE(),
            'max_weight' => $reader->readUint32LE(),
            'intro_date' => $reader->readUint16LE(),
            'retire_date' => $reader->readUint16LE(),
            'wtyp' => $reader->readUint8(),
            'styp' => $reader->readUint8(),
            'draw_as_obj' => $reader->readUint8(),
            'number_of_seasons' => 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion4And5(BinaryReader $reader): array
    {
        return [
            'price' => $reader->readUint32LE(),
            'maintenance' => $reader->readUint32LE(),
            'topspeed' => $reader->readUint32LE(),
            'max_weight' => $reader->readUint32LE(),
            'intro_date' => $reader->readUint16LE(),
            'retire_date' => $reader->readUint16LE(),
            'wtyp' => $reader->readUint8(),
            'styp' => $reader->readUint8(),
            'draw_as_obj' => $reader->readUint8(),
            'number_of_seasons' => $reader->readSint8(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion6(BinaryReader $reader): array
    {
        return [
            'price' => $reader->readUint32LE(),
            'maintenance' => $reader->readUint32LE(),
            'topspeed' => $reader->readUint32LE(),
            'max_weight' => $reader->readUint32LE(),
            'intro_date' => $reader->readUint16LE(),
            'retire_date' => $reader->readUint16LE(),
            'axle_load' => $reader->readUint16LE(),
            'wtyp' => $reader->readUint8(),
            'styp' => $reader->readUint8(),
            'draw_as_obj' => $reader->readUint8(),
            'number_of_seasons' => $reader->readSint8(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion7(BinaryReader $reader): array
    {
        $price = $reader->readUint32LE();
        $reader->readUint32LE(); // upper 32 bits
        $maintenance = $reader->readUint32LE();
        $reader->readUint32LE(); // upper 32 bits

        return [
            'price' => $price,
            'maintenance' => $maintenance,
            'topspeed' => $reader->readUint32LE(),
            'max_weight' => $reader->readUint32LE(),
            'intro_date' => $reader->readUint16LE(),
            'retire_date' => $reader->readUint16LE(),
            'axle_load' => $reader->readUint16LE(),
            'wtyp' => $reader->readUint8(),
            'styp' => $reader->readUint8(),
            'draw_as_obj' => $reader->readUint8(),
            'number_of_seasons' => $reader->readSint8(),
        ];
    }

    /**
     * Apply internal corrections from way_reader.cc
     *
     * @param  array<string, mixed>  $data
     */
    private function applyInternalCorrections(array &$data): void
    {
        if (isset($data['wtyp']) && $data['wtyp'] === 5) { // tram_wt
            $data['styp'] = 7; // type_tram
            $data['wtyp'] = 1; // track_wt
        } elseif (isset($data['styp'], $data['wtyp']) && $data['styp'] === 5 && $data['wtyp'] === 1) {
            $data['wtyp'] = 6; // monorail_wt
            $data['styp'] = 0; // type_flat
        } elseif (isset($data['wtyp']) && $data['wtyp'] === 128) {
            $data['wtyp'] = 7; // powerline_wt
        }
    }
}
