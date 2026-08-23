<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Enums\SimutransClimate;
use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\VersionStamp;

/**
 * Groundobj（地上オブジェクト）パーサー
 *
 * - obj_groundobj（地上オブジェクト）
 * - 小さな湖、岩、花、動物など景観オブジェクトを定義
 * - バージョン 1〜2 に対応（v0 は存在しない）
 *
 * バージョン履歴:
 * - v0: 存在しない（エラー）
 * - v1: allowed_climates、distribution_weight、number_of_seasons、trees_on_top、
 *       speed、wtyp（waytype）、price（sint32）
 * - v2: price を sint32 から sint64 に拡張
 *
 * 特記事項:
 * - speed=0: 静止オブジェクト（groundobj_t）
 * - speed>0: 移動オブジェクト（movingobj_t）
 * - wtyp: 移動可能な地形タイプ（water_t=水上のみ、air_t=全域）
 *
 * @see simutrans/descriptor/reader/groundobj_reader.cc
 */
class GroundobjParser implements TypeParserInterface
{
    private const int MAX_SUPPORTED_VERSION = 2;

    public function canParse(Node $node): bool
    {
        return $node->type === Node::OBJ_GROUNDOBJ;
    }

    /**
     * @return array{
     *     version: int,
     *     allowed_climates: int,
     *     distribution_weight: int,
     *     number_of_seasons: int,
     *     trees_on_top: bool,
     *     speed: int,
     *     waytype: int,
     *     price: int
     * }
     */
    public function parse(Node $node): array
    {
        $stamp = VersionStamp::from($node->data);

        $result = match ($stamp->version) {
            0 => throw InvalidPakFileException::unsupportedTypeVersion('groundobj', 0, self::MAX_SUPPORTED_VERSION),
            1 => $this->parseVersion1($this->readerAfterStamp($node->data)),
            2 => $this->parseVersion2($this->readerAfterStamp($node->data)),
            default => throw InvalidPakFileException::unsupportedTypeVersion('groundobj', $stamp->version, self::MAX_SUPPORTED_VERSION),
        };

        return $this->buildResult($result);
    }

    /**
     * Build a reader positioned just past the 2-byte version stamp.
     *
     * Only called for supported versions, so a too-short payload for the
     * stamp itself is reported via the version-0 branch above instead of a
     * generic EOF error.
     */
    private function readerAfterStamp(string $data): BinaryReader
    {
        $reader = new BinaryReader($data);
        $reader->skip(2);

        return $reader;
    }

    /**
     * Version 1: 基本フィールド、price は sint32
     *
     * @return array{
     *     version: int,
     *     allowed_climates: int,
     *     distribution_weight: int,
     *     number_of_seasons: int,
     *     trees_on_top: int,
     *     speed: int,
     *     waytype: int,
     *     price: int
     * }
     */
    private function parseVersion1(BinaryReader $reader): array
    {
        $allowedClimates = $reader->readUint16LE();
        $distributionWeight = $reader->readUint16LE();
        $numberOfSeasons = $reader->readUint8();
        $treesOnTop = $reader->readUint8();
        $speed = $reader->readUint16LE();
        $waytype = $reader->readUint16LE();
        $price = $reader->readSint32LE();

        return [
            'version' => 1,
            'allowed_climates' => $allowedClimates,
            'distribution_weight' => $distributionWeight,
            'number_of_seasons' => $numberOfSeasons,
            'trees_on_top' => $treesOnTop,
            'speed' => $speed,
            'waytype' => $waytype,
            'price' => $price,
        ];
    }

    /**
     * Version 2: price を sint64 に拡張
     *
     * @return array{
     *     version: int,
     *     allowed_climates: int,
     *     distribution_weight: int,
     *     number_of_seasons: int,
     *     trees_on_top: int,
     *     speed: int,
     *     waytype: int,
     *     price: int
     * }
     */
    private function parseVersion2(BinaryReader $reader): array
    {
        $allowedClimates = $reader->readUint16LE();
        $distributionWeight = $reader->readUint16LE();
        $numberOfSeasons = $reader->readUint8();
        $treesOnTop = $reader->readUint8();
        $speed = $reader->readUint16LE();
        $waytype = $reader->readUint16LE();
        $price = $reader->readSint64LE();

        return [
            'version' => 2,
            'allowed_climates' => $allowedClimates,
            'distribution_weight' => $distributionWeight,
            'number_of_seasons' => $numberOfSeasons,
            'trees_on_top' => $treesOnTop,
            'speed' => $speed,
            'waytype' => $waytype,
            'price' => $price,
        ];
    }

    /**
     * 気候名と waytype 名の文字列表現を追加、trees_on_top を bool に変換
     *
     * @param  array{
     *     version: int,
     *     allowed_climates: int,
     *     distribution_weight: int,
     *     number_of_seasons: int,
     *     trees_on_top: int,
     *     speed: int,
     *     waytype: int,
     *     price: int
     * }  $data
     * @return array{
     *     version: int,
     *     allowed_climates: int,
     *     distribution_weight: int,
     *     number_of_seasons: int,
     *     trees_on_top: bool,
     *     speed: int,
     *     waytype: int,
     *     price: int
     * }
     */
    private function buildResult(array $data): array
    {
        $climateNames = SimutransClimate::fromBitFlags($data['allowed_climates']);

        return [
            'version' => $data['version'],
            'allowed_climates' => $data['allowed_climates'],
            'climate_names' => $climateNames,
            'distribution_weight' => $data['distribution_weight'],
            'number_of_seasons' => $data['number_of_seasons'],
            'trees_on_top' => (bool) $data['trees_on_top'],
            'speed' => $data['speed'],
            'waytype' => $data['waytype'],
            'price' => $data['price'],
        ];
    }
}
