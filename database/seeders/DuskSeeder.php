<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Duskテスト用シーダー
 */
final class DuskSeeder extends Seeder
{
    /**
     * 管理者とカテゴリを追加する.
     */
    public function run(): void
    {
        DB::statement('delete from articles');
        DB::statement('delete from users');
        $this->call(CategorySeeder::class);
        $this->call(ControllOptionsSeeder::class);
    }
}
