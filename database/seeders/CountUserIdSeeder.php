<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class CountUserIdSeeder extends Seeder
{
    /**
     * user_idが未設定(0)のレコードを更新する
     */
    public function run(): void
    {
        DB::statement('UPDATE view_counts AS c JOIN articles AS a ON c.article_id = a.id SET c.user_id = a.user_id WHERE c.user_id=0');
        DB::statement('UPDATE conversion_counts AS c JOIN articles AS a ON c.article_id = a.id SET c.user_id = a.user_id WHERE c.user_id=0');
    }
}
