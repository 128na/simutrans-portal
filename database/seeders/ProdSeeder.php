<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * 本番環境用シーダー
 */
class ProdSeeder extends Seeder
{
    /**
     * 管理者とカテゴリを追加する.
     */
    public function run(): void
    {
        $this->call(CategorySeeder::class);
        $this->call(ControllOptionsSeeder::class);
        $this->call(AdminSeeder::class);
    }
}
