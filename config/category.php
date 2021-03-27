<?php

return [
    'type' => [
        'post' => 'post',
        'pak' => 'pak',
        'addon' => 'addon',
        'pak128_position' => 'pak128_position',
        'license' => 'license',
        'page' => 'page',
    ],
    'post' => [
        ['slug' => 'addon-post',         'type' => 'post', 'order' => 00000, 'name' => 'Addon Post'],
        ['slug' => 'addon-introduction', 'type' => 'post', 'order' => 00010, 'name' => 'Addon Introduction'],
        ['slug' => 'page',               'type' => 'post', 'order' => 00020, 'name' => 'Post'],
    ],
    'pak' => [
        ['slug' => '64',           'type' => 'pak', 'order' => 10000, 'name' => 'Pak64'],
        ['slug' => '128',          'type' => 'pak', 'order' => 10010, 'name' => 'Pak128'],
        ['slug' => '128-japan',    'type' => 'pak', 'order' => 10011, 'name' => 'Pak128.Japan'],
        ['slug' => '256',          'type' => 'pak', 'order' => 10020, 'name' => 'Pak256'],
        ['slug' => '96-comic',     'type' => 'pak', 'order' => 10030, 'name' => 'Pak96.Comic'],
        ['slug' => '192-comic',    'type' => 'pak', 'order' => 10031, 'name' => 'Pak192.Comic'],
        ['slug' => '256-extended', 'type' => 'pak', 'order' => 10040, 'name' => 'Pak256(Extended)'],
        ['slug' => 'others',       'type' => 'pak', 'order' => 19000, 'name' => 'Others'],
    ],
    'addon' => [
        ['slug' => 'trains',               'type' => 'addon', 'order' => 20010, 'name' => 'Trains'],
        ['slug' => 'rail-tools',           'type' => 'addon', 'order' => 20020, 'name' => 'Rail tools'],
        ['slug' => 'road-tools',           'type' => 'addon', 'order' => 20030, 'name' => 'Road tools'],
        ['slug' => 'ships',                'type' => 'addon', 'order' => 20040, 'name' => 'Ships'],
        ['slug' => 'aircrafts',            'type' => 'addon', 'order' => 20050, 'name' => 'Aircrafts'],
        ['slug' => 'road-vehicles',        'type' => 'addon', 'order' => 20060, 'name' => 'Road vehicles'],
        ['slug' => 'airport-tools',        'type' => 'addon', 'order' => 20070, 'name' => 'Airport tools'],
        ['slug' => 'industrial-tools',     'type' => 'addon', 'order' => 20080, 'name' => 'Industrial tools'],
        ['slug' => 'seaport-tools',        'type' => 'addon', 'order' => 20090, 'name' => 'Seaport tools'],
        ['slug' => 'buildings',            'type' => 'addon', 'order' => 20100, 'name' => 'Buildings'],
        ['slug' => 'monorail-vehicles',    'type' => 'addon', 'order' => 20110, 'name' => 'Monorail vehicles'],
        ['slug' => 'monorail-tools',       'type' => 'addon', 'order' => 20120, 'name' => 'Monorail tools'],
        ['slug' => 'maglev-vehicles',      'type' => 'addon', 'order' => 20130, 'name' => 'Maglev vehicles'],
        ['slug' => 'maglev-tools',         'type' => 'addon', 'order' => 20140, 'name' => 'Maglev tools'],
        ['slug' => 'narrow-gauge-vahicle', 'type' => 'addon', 'order' => 20150, 'name' => 'Narrow gauge vahicle'],
        ['slug' => 'narrow-gauge-tools',   'type' => 'addon', 'order' => 20160, 'name' => 'Narrow gauge tools'],
        ['slug' => 'tram-vehicle',         'type' => 'addon', 'order' => 20170, 'name' => 'Tram vehicle'],
        ['slug' => 'tram-tools',           'type' => 'addon', 'order' => 20180, 'name' => 'Tram tools'],
        ['slug' => 'scripts',              'type' => 'addon', 'order' => 20200, 'name' => 'Scripts'],
        ['slug' => 'others',               'type' => 'addon', 'order' => 29000, 'name' => 'Others'],
    ],
    'pak128_position' => [
        ['slug' => 'old', 'type' => 'pak128_position', 'order' => 30000, 'name' => 'Old position (before 112.3)'],
        ['slug' => 'new', 'type' => 'pak128_position', 'order' => 30010, 'name' => 'New position (after 120.0)'],
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
        ['slug' => 'others',      'type' => 'license', 'order' => 49000, 'name' => 'Others'],
    ],
    'page' => [
        ['slug' => 'announce', 'type' => 'page', 'order' => 50000, 'name' => 'Announce', 'need_admin' => true],
        ['slug' => 'common',   'type' => 'page', 'order' => 50010, 'name' => 'Common'],
        ['slug' => 'others',   'type' => 'page', 'order' => 59000, 'name' => 'Others'],
    ],
];
