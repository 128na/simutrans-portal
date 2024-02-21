<?php

declare(strict_types=1);

namespace Database\Seeders\Updates;

use App\Models\Category;
use Illuminate\Database\Seeder;

class PakAnySeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::firstOrCreate([
            'slug' => 'any',
        ], [
            'slug' => 'any',
            'type' => 'pak',
            'order' => 9000,
            'name' => 'Any',
        ]);
    }
}
