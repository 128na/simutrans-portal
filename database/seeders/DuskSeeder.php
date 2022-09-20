<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Duskテスト用シーダー
 */
class DuskSeeder extends Seeder
{
    /**
     * 管理者とカテゴリを追加する.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('delete from articles');
        DB::statement('delete from users');
        $this->call(ProdSeeder::class);
    }
}
