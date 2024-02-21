<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ProdSeeder::class);

        if (App::environment(['local', 'development'])) {
            // $this->call(DevSeeder::class);
        }
    }
}
