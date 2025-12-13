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
    'width' => env('THUMBNAIL_WIDTH', 300),

    // サムネイル画像の出力フォーマット（webp, jpeg, png）
    'format' => env('THUMBNAIL_FORMAT', 'webp'),

    // サムネイルの保存先ディレクトリ
    'directory' => 'thumbnails',
];
