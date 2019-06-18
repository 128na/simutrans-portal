<?php

return [
    'type' => [
        'post'            => 'post',
        'pak'             => 'pak',
        'addon'           => 'addon',
        'pak128_position' => 'pak128_position',
        'license'         => 'license',
    ],
    'post' => [
        ['slug' => 'addon-post',         'type' => 'post', 'order' => 00000, 'name' => 'アドオン投稿'],
        ['slug' => 'addon-introduction', 'type' => 'post', 'order' => 00010, 'name' => 'アドオン紹介'],
    ],
    'pak' => [
        ['slug' => '64',           'type' => 'pak', 'order' => 10000, 'name' => 'pak64'],
        ['slug' => '128',          'type' => 'pak', 'order' => 10010, 'name' => 'pak128'],
        ['slug' => '128-japan',    'type' => 'pak', 'order' => 10011, 'name' => 'pak128.Japan'],
        ['slug' => '256',          'type' => 'pak', 'order' => 10020, 'name' => 'pak256'],
        ['slug' => '96-comic',     'type' => 'pak', 'order' => 10030, 'name' => 'pak96.Comic'],
        ['slug' => '192-comic',    'type' => 'pak', 'order' => 10031, 'name' => 'pak192.Comic'],
        ['slug' => '256-extended', 'type' => 'pak', 'order' => 10040, 'name' => 'pak256(Extended)'],
        ['slug' => '256-extended', 'type' => 'pak', 'order' => 19000, 'name' => 'pak256(Extended)'],
    ],
    'addon' => [
        ['slug' => 'trains',               'type' => 'addon', 'order' => 20010, 'name'=> '鉄道車両'],
        ['slug' => 'rail-tools',           'type' => 'addon', 'order' => 20020, 'name'=> '鉄道施設'],
        ['slug' => 'road-tools',           'type' => 'addon', 'order' => 20030, 'name'=> '道路施設'],
        ['slug' => 'ships',                'type' => 'addon', 'order' => 20040, 'name'=> '船舶'],
        ['slug' => 'aircrafts',            'type' => 'addon', 'order' => 20050, 'name'=> '航空機'],
        ['slug' => 'road-vehicles',        'type' => 'addon', 'order' => 20060, 'name'=> '自動車'],
        ['slug' => 'airport-tools',        'type' => 'addon', 'order' => 20070, 'name'=> '空港施設'],
        ['slug' => 'industrial-tools',     'type' => 'addon', 'order' => 20080, 'name'=> '産業'],
        ['slug' => 'seaport-tools',        'type' => 'addon', 'order' => 20090, 'name'=> '港湾施設'],
        ['slug' => 'buildings',            'type' => 'addon', 'order' => 20100, 'name'=> '一般建築物'],
        ['slug' => 'monorail-vehicles',    'type' => 'addon', 'order' => 20110, 'name'=> 'モノレール車両'],
        ['slug' => 'monorail-tools',       'type' => 'addon', 'order' => 20120, 'name'=> 'モノレール施設'],
        ['slug' => 'maglev-vehicles',      'type' => 'addon', 'order' => 20130, 'name'=> 'マグレブ(リニア)車両'],
        ['slug' => 'maglev-tools',         'type' => 'addon', 'order' => 20140, 'name'=> 'マグレブ(リニア)施設'],
        ['slug' => 'narrow-gauge-vahicle', 'type' => 'addon', 'order' => 20150, 'name'=> 'ナローゲージ車両'],
        ['slug' => 'narrow-gauge-tools',   'type' => 'addon', 'order' => 20160, 'name'=> 'ナローゲージ施設'],
        ['slug' => 'tram-vehicle',         'type' => 'addon', 'order' => 20170, 'name'=> 'トラム車両'],
        ['slug' => 'tram-tools',           'type' => 'addon', 'order' => 20180, 'name'=> 'トラム施設'],
        ['slug' => 'others',               'type' => 'addon', 'order' => 29000, 'name'=> 'その他'],
    ],
    'pak128_position' => [
        ['slug' => 'old', 'type' => 'pak128_position', 'order' => 30000, 'name' => '旧描画位置（主に112.3以前）'],
        ['slug' => 'new', 'type' => 'pak128_position', 'order' => 30010, 'name' => '新描画位置（主に120.0以降）'],
    ],
    'license' => [
        ['slug' => 'cc-by',       'type' => 'license', 'order' => 40000, 'name' => 'CC BY'],
        ['slug' => 'cc-BY-nc',    'type' => 'license', 'order' => 40001, 'name' => 'CC BY-NC'],
        ['slug' => 'cc-BY-nd',    'type' => 'license', 'order' => 40002, 'name' => 'CC BY-ND'],
        ['slug' => 'cc-BY-NC-nd', 'type' => 'license', 'order' => 40003, 'name' => 'CC BY-NC-ND'],
        ['slug' => 'cc-BY-sa',    'type' => 'license', 'order' => 40004, 'name' => 'CC BY-SA'],
        ['slug' => 'cc-BY-NC-sa', 'type' => 'license', 'order' => 40005, 'name' => 'CC BY-NC-SA'],
        ['slug' => 'cc0',         'type' => 'license', 'order' => 40006, 'name' => 'CC0'],
        ['slug' => 'pdm',         'type' => 'license', 'order' => 40100, 'name' => 'PDM'],
        ['slug' => 'mit-license', 'type' => 'license', 'order' => 40200, 'name' => 'MIT License'],
        ['slug' => 'others',      'type' => 'license', 'order' => 49000, 'name' => 'その他'],
    ]

];
