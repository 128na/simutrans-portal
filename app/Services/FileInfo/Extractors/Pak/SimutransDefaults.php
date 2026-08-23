<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak;

/**
 * Simutrans本家 descriptor/intro_dates.h のデフォルト値
 *
 * @see https://github.com/aburch/simutrans/blob/master/src/simutrans/descriptor/intro_dates.h
 */
final class SimutransDefaults
{
    public const int INTRO_YEAR = 1900;

    public const int RETIRE_YEAR = 2999;

    /**
     * vehicle_reader.cc / way_reader.cc: v5未満のintro_date/retire_dateは
     * year*16+month のbase-16パック形式で保存されており、year*12+month の
     * 現行形式に変換する必要がある。
     *
     * 例外: CitycarParser の base-16→12 変換は citycar_reader.cc 独自の式
     * （剰余だけ %12 を使う）であり、この定数とは別に扱う。詳細は
     * CitycarParser::parseVersion1() のコメントを参照。
     */
    public const int LEGACY_DATE_BASE = 16;

    /**
     * 現行のintro_date/retire_dateのパック形式 (year*12+month)。
     */
    public const int CURRENT_DATE_BASE = 12;

    /**
     * vehicle_reader.cc: 重量はuint16のtonをkgへ換算するためraw*1000として
     * 保存する（v10以降はuint32でkgを直接保存するためこの換算は不要）。
     *
     * 同じ値がフロント側 resources/js/features/articles/components/pak/pakConstants.ts
     * の PAK_UNIT_SCALES.WEIGHT にも存在する（表示時にkg→tonへ逆変換するため）。
     * 値を変更する場合は両方を更新すること。
     */
    public const int WEIGHT_SCALE = 1000;
}
