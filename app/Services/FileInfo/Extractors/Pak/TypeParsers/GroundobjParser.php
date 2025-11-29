<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Enums\SimutransClimate;
use App\Services\FileInfo\Extractors\Pak\Node;
use RuntimeException;

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
final readonly class GroundobjParser implements TypeParserInterface
{
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
        $firstUint16 = (unpack('v', substr($node->data, 0, 2)) ?: [])[1] ?? 0;
        $version = (($firstUint16 & 0x8000) !== 0) ? ($firstUint16 & 0x7FFF) : 0;

        $result = match ($version) {
            0 => throw new RuntimeException('Groundobj version 0 does not exist'),
            1 => $this->parseVersion1($node->data),
            2 => $this->parseVersion2($node->data),
            default => throw new RuntimeException('Unsupported groundobj version: '.$version),
        };

        return $this->buildResult($result);
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
    private function parseVersion1(string $data): array
    {
        $unpacked = unpack(
            'vversion/vallowed_climates/vdistribution_weight/Cnumber_of_seasons/Ctrees_on_top/vspeed/vwaytype/lprice',
            substr($data, 0, 16)
        ) ?: [];

        return [
            'version' => $unpacked['version'] & 0x7FFF,
            'allowed_climates' => $unpacked['allowed_climates'],
            'distribution_weight' => $unpacked['distribution_weight'],
            'number_of_seasons' => $unpacked['number_of_seasons'],
            'trees_on_top' => $unpacked['trees_on_top'],
            'speed' => $unpacked['speed'],
            'waytype' => $unpacked['waytype'],
            'price' => $unpacked['price'],
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
    private function parseVersion2(string $data): array
    {
        $unpacked = unpack(
            'vversion/vallowed_climates/vdistribution_weight/Cnumber_of_seasons/Ctrees_on_top/vspeed/vwaytype',
            substr($data, 0, 12)
        ) ?: [];

        // sint64 は P (64-bit little-endian)
        $priceData = substr($data, 12, 8);
        $price = (unpack('P', $priceData) ?: [])[1] ?? 0;

        return [
            'version' => $unpacked['version'] & 0x7FFF,
            'allowed_climates' => $unpacked['allowed_climates'],
            'distribution_weight' => $unpacked['distribution_weight'],
            'number_of_seasons' => $unpacked['number_of_seasons'],
            'trees_on_top' => $unpacked['trees_on_top'],
            'speed' => $unpacked['speed'],
            'waytype' => $unpacked['waytype'],
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
