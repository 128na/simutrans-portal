<?php

namespace Database\Seeders\Updates;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * アプリケーションカテゴリ追加用シーダー
 */
class AddApplicationCategorySeeder extends Seeder
{
    public function run()
    {
        Category::firstOrCreate([
            'slug' => 'application',
        ], [
            'slug' => 'application',
            'type' => 'page',
            'order' => 50200,
            'name' => 'Application'
        ]);
    }
}
