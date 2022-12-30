<?php

declare(strict_types=1);

namespace Database\Seeders\Updates;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * 追加したpublished_at, modified_atにcreated_at, updated_atの値を登録する.
 */
class UpdatePublishedAtSeeder extends Seeder
{
    public function run()
    {
        DB::statement('UPDATE articles SET published_at = created_at, modified_at = updated_at');
    }
}
