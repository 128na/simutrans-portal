<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * カテゴリ
 */
final class CategorySeeder extends Seeder
{
    private const CATEGORIES = [
        // pak
        ['slug' => 'any',          'type' => CategoryType::Pak, 'order' => 9000],
        ['slug' => '128-japan',    'type' => CategoryType::Pak, 'order' => 10000],
        ['slug' => '128',          'type' => CategoryType::Pak, 'order' => 10010],
        ['slug' => '64',           'type' => CategoryType::Pak, 'order' => 10011],
        ['slug' => '256',          'type' => CategoryType::Pak, 'order' => 10020],
        ['slug' => '96-comic',     'type' => CategoryType::Pak, 'order' => 10030],
        ['slug' => '192-comic',    'type' => CategoryType::Pak, 'order' => 10031],
        ['slug' => '256-extended', 'type' => CategoryType::Pak, 'order' => 10040],
        ['slug' => 'others',       'type' => CategoryType::Pak, 'order' => 19000],
        // addon
        ['slug' => 'trains',               'type' => CategoryType::Addon, 'order' => 20010],
        ['slug' => 'rail-tools',           'type' => CategoryType::Addon, 'order' => 20020],
        ['slug' => 'road-tools',           'type' => CategoryType::Addon, 'order' => 20030],
        ['slug' => 'ships',                'type' => CategoryType::Addon, 'order' => 20040],
        ['slug' => 'aircrafts',            'type' => CategoryType::Addon, 'order' => 20050],
        ['slug' => 'road-vehicles',        'type' => CategoryType::Addon, 'order' => 20060],
        ['slug' => 'airport-tools',        'type' => CategoryType::Addon, 'order' => 20070],
        ['slug' => 'industrial-tools',     'type' => CategoryType::Addon, 'order' => 20080],
        ['slug' => 'seaport-tools',        'type' => CategoryType::Addon, 'order' => 20090],
        ['slug' => 'buildings',            'type' => CategoryType::Addon, 'order' => 20100],
        ['slug' => 'monorail-vehicles',    'type' => CategoryType::Addon, 'order' => 20110],
        ['slug' => 'monorail-tools',       'type' => CategoryType::Addon, 'order' => 20120],
        ['slug' => 'maglev-vehicles',      'type' => CategoryType::Addon, 'order' => 20130],
        ['slug' => 'maglev-tools',         'type' => CategoryType::Addon, 'order' => 20140],
        ['slug' => 'narrow-gauge-vahicle', 'type' => CategoryType::Addon, 'order' => 20150],
        ['slug' => 'narrow-gauge-tools',   'type' => CategoryType::Addon, 'order' => 20160],
        ['slug' => 'tram-vehicle',         'type' => CategoryType::Addon, 'order' => 20170],
        ['slug' => 'tram-tools',           'type' => CategoryType::Addon, 'order' => 20180],
        ['slug' => 'scripts',              'type' => CategoryType::Addon, 'order' => 20200],
        ['slug' => 'others',               'type' => CategoryType::Addon, 'order' => 29000],
        // pak128_position
        ['slug' => 'old', 'type' => CategoryType::Pak128Position, 'order' => 30000],
        ['slug' => 'new', 'type' => CategoryType::Pak128Position, 'order' => 30010],
        // license
        ['slug' => 'cc-by',       'type' => CategoryType::License, 'order' => 40000],
        ['slug' => 'cc-BY-nc',    'type' => CategoryType::License, 'order' => 40001],
        ['slug' => 'cc-BY-nd',    'type' => CategoryType::License, 'order' => 40002],
        ['slug' => 'cc-BY-NC-nd', 'type' => CategoryType::License, 'order' => 40003],
        ['slug' => 'cc-BY-sa',    'type' => CategoryType::License, 'order' => 40004],
        ['slug' => 'cc-BY-NC-sa', 'type' => CategoryType::License, 'order' => 40005],
        ['slug' => 'cc0',         'type' => CategoryType::License, 'order' => 40006],
        ['slug' => 'pdm',         'type' => CategoryType::License, 'order' => 40100],
        ['slug' => 'mit-license', 'type' => CategoryType::License, 'order' => 40200],
        ['slug' => 'others',      'type' => CategoryType::License, 'order' => 49000],
        // page
        ['slug' => 'announce', 'type' => CategoryType::Page, 'order' => 50000],
        ['slug' => 'common',   'type' => CategoryType::Page, 'order' => 50010],
        ['slug' => 'others',   'type' => CategoryType::Page, 'order' => 59000],
    ];

    public function run(): void
    {
        foreach (self::CATEGORIES as $category) {
            Category::updateOrCreate([
                'slug' => $category['slug'],
                'type' => $category['type'],
            ], [
                'order' => $category['order'],
            ]);
        }
    }
}
