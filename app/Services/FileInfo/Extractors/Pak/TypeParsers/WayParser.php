<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\SimutransDefaults;

/**
 * Way data parser
 *
 * 参考: simutrans/descriptor/reader/way_reader.cc
 */
class WayParser implements TypeParserInterface
{
    private const int MAX_SUPPORTED_VERSION = 8;

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
            8 => $this->parseVersion8($reader),
            default => throw InvalidPakFileException::unsupportedTypeVersion('way', $version, self::MAX_SUPPORTED_VERSION),
        };

        // Apply internal corrections (from way_reader.cc)
        $this->applyInternalCorrections($data);

        // front_images from version 5 on (from way_reader.cc)
        $data['front_images'] = $version > 4;

        // axle_load defaults to 9999 for versions < 6
        if ($version < 6 && ! isset($data['axle_load'])) {
            $data['axle_load'] = 9999;
        }

        // clip_below defaults based on waytype for versions < 8 (from way_reader.cc)
        if ($version < 8) {
            $data['clip_below'] = ($data['waytype'] ?? 0) !== 128; // 128 = powerline_wt
        }

        if (isset($data['styp'])) {
            assert(is_int($data['styp']));
        }

        return $data;
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
            'intro_date' => SimutransDefaults::INTRO_YEAR * 12,
            'retire_date' => SimutransDefaults::RETIRE_YEAR * 12,
            'waytype' => 1,
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
        $price = $reader->readUint32LE();
        $maintenance = $reader->readUint32LE();
        $topspeed = $reader->readUint32LE();
        $maxWeight = $reader->readUint32LE();
        $introDateRaw = $reader->readUint32LE();

        return [
            'price' => $price,
            'maintenance' => $maintenance,
            'topspeed' => $topspeed,
            'max_weight' => $maxWeight,
            'intro_date' => intdiv($introDateRaw, 16) * 12 + ($introDateRaw % 16),
            'waytype' => $reader->readUint8(),
            'styp' => $reader->readUint8(),
            'retire_date' => SimutransDefaults::RETIRE_YEAR * 12,
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
            'waytype' => $reader->readUint8(),
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
            'waytype' => $reader->readUint8(),
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
            'waytype' => $reader->readUint8(),
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
            'waytype' => $reader->readUint8(),
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
        $price = $reader->readSint64LE();
        $maintenance = $reader->readSint64LE();

        return [
            'price' => $price,
            'maintenance' => $maintenance,
            'topspeed' => $reader->readUint32LE(),
            'max_weight' => $reader->readUint32LE(),
            'intro_date' => $reader->readUint16LE(),
            'retire_date' => $reader->readUint16LE(),
            'axle_load' => $reader->readUint16LE(),
            'waytype' => $reader->readUint8(),
            'styp' => $reader->readUint8(),
            'draw_as_obj' => $reader->readUint8(),
            'number_of_seasons' => $reader->readSint8(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseVersion8(BinaryReader $reader): array
    {
        $price = $reader->readSint64LE();
        $maintenance = $reader->readSint64LE();

        return [
            'price' => $price,
            'maintenance' => $maintenance,
            'topspeed' => $reader->readUint32LE(),
            'max_weight' => $reader->readUint32LE(),
            'intro_date' => $reader->readUint16LE(),
            'retire_date' => $reader->readUint16LE(),
            'axle_load' => $reader->readUint16LE(),
            'waytype' => $reader->readUint8(),
            'styp' => $reader->readUint8(),
            'draw_as_obj' => $reader->readUint8(),
            'clip_below' => $reader->readUint8(),
            'number_of_seasons' => $reader->readSint8(),
        ];
    }

    /**
     * Apply internal corrections from way_reader.cc
     *
     * Current waytype_t/systemtype_t values (simtypes.h):
     * road=1, track=2, water=3, overheadlines=4, monorail=5, maglev=6,
     * tram=7, narrowgauge=8, powerline=128; type_flat=0, type_tram=7.
     *
     * @param  array<string, mixed>  $data
     */
    private function applyInternalCorrections(array &$data): void
    {
        if (isset($data['waytype']) && $data['waytype'] === 7) { // tram_wt
            $data['styp'] = 7; // type_tram
            $data['waytype'] = 2; // track_wt
        } elseif (isset($data['styp'], $data['waytype']) && $data['styp'] === 5 && $data['waytype'] === 2) {
            $data['waytype'] = 5; // monorail_wt
            $data['styp'] = 0; // type_flat
        }
        // powerline_wt(128) の補正は現行の番号体系では無変化 (no-op) のため実施しない
    }
}
