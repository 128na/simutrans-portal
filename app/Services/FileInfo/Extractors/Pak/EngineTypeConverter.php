<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak;

/**
 * エンジンタイプコンバーター
 *
 * Simutrans の engine_type (uint8) を文字列に変換
 * 参考: simutrans/descriptor/vehicle_desc.h の engine_t enum
 */
final readonly class EngineTypeConverter
{
    /**
     * エンジンタイプを文字列に変換
     *
     * @param  int  $engineType  エンジンタイプ (0-7)
     * @return string エンジンタイプ名
     */
    public static function convert(int $engineType): string
    {
        return match ($engineType) {
            0 => 'steam',       // 蒸気機関
            1 => 'diesel',      // ディーゼル
            2 => 'electric',    // 電気
            3 => 'bio',         // バイオ燃料
            4 => 'sail',        // 帆
            5 => 'fuel_cell',   // 燃料電池
            6 => 'hydrogene',   // 水素
            7 => 'battery',     // バッテリー
            default => 'unknown',
        };
    }
}
