<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Simutrans気候タイプ
 *
 * Simutransで使用される8種類の気候帯を定義します。
 * 各オブジェクト（建物、木、地上オブジェクト等）は、どの気候帯に出現するかを
 * ビットフラグで指定します。
 *
 * @see simutrans/simconst.h - MAX_CLIMATES
 */
enum SimutransClimate: int
{
    case Water = 0;
    // 水域
    case Desert = 1;
    // 砂漠
    case Tropic = 2;
    // 熱帯
    case Mediterran = 3;
    // 地中海性
    case Temperate = 4;
    // 温帯
    case Tundra = 5;
    // ツンドラ
    case Rocky = 6;
    // 岩地
    case Arctic = 7;       // 極地

    /**
     * 気候名を取得
     */
    public function label(): string
    {
        return match ($this) {
            self::Water => 'water_climate',
            self::Desert => 'desert_climate',
            self::Tropic => 'tropic_climate',
            self::Mediterran => 'mediterran_climate',
            self::Temperate => 'temperate_climate',
            self::Tundra => 'tundra_climate',
            self::Rocky => 'rocky_climate',
            self::Arctic => 'arctic_climate',
        };
    }

    /**
     * ビットフラグから該当する気候のリストを取得
     *
     * @param  int  $climateFlags  気候ビットフラグ（8ビット）
     * @return array<string> 気候名の配列
     */
    public static function fromBitFlags(int $climateFlags): array
    {
        $climates = [];
        foreach (self::cases() as $climate) {
            if (($climateFlags & (1 << $climate->value)) !== 0) {
                $climates[] = $climate->label();
            }
        }

        return $climates;
    }

    /**
     * 日本語名を取得
     */
    public function labelJa(): string
    {
        return match ($this) {
            self::Water => '水域',
            self::Desert => '砂漠',
            self::Tropic => '熱帯',
            self::Mediterran => '地中海性',
            self::Temperate => '温帯',
            self::Tundra => 'ツンドラ',
            self::Rocky => '岩地',
            self::Arctic => '極地',
        };
    }
}
