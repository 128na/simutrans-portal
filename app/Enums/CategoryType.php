<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * カテゴリの分類
 */
enum CategoryType: string
{
    /**
     * パックセットの種類
     */
    case Pak = 'pak';

    /**
     * アドオンの種類
     */
    case Addon = 'addon';

    /**
     * pak128車両描画位置の種類
     */
    case Pak128Position = 'pak128_position';

    /**
     * 緩急坂の種類
     */
    case DoubleSlope = 'double_slope';

    /**
     * ライセンスの種類
     */
    case License = 'license';

    /**
     * 一般記事用
     */
    case Page = 'page';
}
