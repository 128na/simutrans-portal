<?php

declare(strict_types=1);

/**
 * カテゴリ.
 */
return [
    'type' => [
        'pak' => 'Pak一覧',
        'addon' => '種類一覧',
        'pak128_position' => 'Pak128用描画位置',
        'license' => 'ライセンス',
        'page' => 'カテゴリ',
        'double_slope' => '緩急坂',
    ],
    'pak' => [
        'any' => 'Pak問わず',
        '64' => 'Pak64',
        '128' => 'Pak128',
        '128-japan' => 'Pak128.Japan',
        '256' => 'Pak256',
        '96-comic' => 'Pak96.Comic',
        '192-comic' => 'Pak192.Comic',
        '256-extended' => 'Pak256(Extended)',
        'others' => 'その他',
        'other-pak' => '他pak',
    ],
    'addon' => [
        'trains' => '鉄道車両',
        'rail-tools' => '鉄道施設',
        'road-tools' => '道路施設',
        'ships' => '船舶',
        'aircrafts' => '航空機',
        'road-vehicles' => '自動車',
        'airport-tools' => '空港施設',
        'industrial-tools' => '産業',
        'seaport-tools' => '港湾施設',
        'buildings' => '一般建築物',
        'monorail-vehicles' => 'モノレール車両',
        'monorail-tools' => 'モノレール施設',
        'maglev-vehicles' => 'マグレブ(リニア)車両',
        'maglev-tools' => 'マグレブ(リニア)施設',
        'narrow-gauge-vahicle' => 'ナローゲージ車両',
        'narrow-gauge-tools' => 'ナローゲージ施設',
        'tram-vehicle' => 'トラム車両',
        'tram-tools' => 'トラム施設',
        'scripts' => 'スクリプト',
        'others' => 'その他',
        'none' => '未指定',
    ],
    'pak128_position' => [
        'old' => '旧描画位置（主に112.3以前）',
        'new' => '新描画位置（主に120.0以降）',
    ],
    'double_slope' => [
        'double' => '急坂',
        'half' => '緩坂',
    ],
    'license' => [
        'cc-by' => 'CC BY',
        'cc-by-nc' => 'CC BY-NC',
        'cc-by-nd' => 'CC BY-ND',
        'cc-by-nc-nd' => 'CC BY-NC-ND',
        'cc-by-sa' => 'CC BY-SA',
        'cc-by-nc-sa' => 'CC BY-NC-SA',
        'cc0' => 'CC0',
        'pdm' => 'パブリックドメイン',
        'mit-license' => 'MIT License',
        'others' => 'その他',
    ],
    'page' => [
        'announce' => 'お知らせ',
        'common' => '一般記事',
        'application' => 'アプリケーション',
        'others' => 'その他',
    ],
    // 記事編集画面とカテゴリ別一覧で表示する
    'description' => [
        'pak' => [
            '64' => '公式の Pak64 はもっとも古くから存在します。Pak.nippon など各種派生版のアドオンも含まれています。',
            '128' => '公式の Pak128 はpak64よりも大きなグラフィックが特徴です。',
            '128-japan' => 'Pak128.Japan は日本の車両を中心に揃えた Pak128 の派生版です。Pak128と車両のスケールが異なるほか、産業の種類も異なるため互換性のないアドオンもあります。',
            'others' => 'Pak64, 128, 128.Japan以外のpakセット向けアドオンの一覧です。設定ファイルやメニューバーなど、pakセットに関係なく使えるコンテンツも含まれます。',
        ],
    ],
];
