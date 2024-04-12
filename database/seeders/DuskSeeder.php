<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Duskテスト用シーダー
 */
final class DuskSeeder extends Seeder
{
    public function run(): void
    {
        Article::truncate();
        User::truncate();
        $this->call(CategorySeeder::class);
        $this->call(ControllOptionsSeeder::class);
    }
}
