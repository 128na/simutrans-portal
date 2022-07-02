<?php

namespace Database\Seeders\Updates;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 追加したpublished_atにupdated_atの値を登録する.
 */
class UpdatePublishedAtSeeder extends Seeder
{
    public function run()
    {
        DB::statement('UPDATE articles SET published_at = updated_at');
    }
}
