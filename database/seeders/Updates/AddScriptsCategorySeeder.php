<?php

namespace Database\Seeders\Updates;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * スクリプトカテゴリ追加用シーダー
 */
class AddScriptsCategorySeeder extends Seeder
{
    public function run()
    {
        Category::firstOrCreate([
            'slug' => 'scripts',
        ], [
            'slug' => 'scripts',
            'type' => 'addon',
            'order' => 20200,
            'name' => 'Scripts',
        ]);
    }
}
