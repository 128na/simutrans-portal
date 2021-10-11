<?php

namespace Database\Seeders\Updates;

use App\Models\Category;
use Illuminate\Database\Seeder;

class AddPak192Categories extends Seeder
{
    public function run()
    {
        Category::firstOrCreate([
            'slug' => '192-xxx',
        ], [
            'slug' => '192-xxx',
            'type' => 'pak',
            'order' => 10021,
            'name' => 'Pak192.xxx',
        ]);
    }
}
