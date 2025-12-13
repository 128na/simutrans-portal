<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Thumbnail Configuration
    |--------------------------------------------------------------------------
    |
    | サムネイル画像の生成設定
    |
    */

    // サムネイル画像の幅（ピクセル）
    'width' => env('THUMBNAIL_WIDTH', 720),

    // サムネイル画像の出力フォーマット（webp, jpeg, png）
    'format' => env('THUMBNAIL_FORMAT', 'webp'),

    // WebP/JPEGの品質（0-100、高いほど高品質・大きいファイル）
    'quality' => env('THUMBNAIL_QUALITY', 100),

    // PNGの圧縮レベル（0-9、低いほど大きいファイル・高品質）
    'png_compression' => env('THUMBNAIL_PNG_COMPRESSION', 5),

    // サムネイルの保存先ディレクトリ
    'directory' => 'thumbnails',
];
