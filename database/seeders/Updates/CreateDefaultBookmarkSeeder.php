<?php

namespace Database\Seeders\Updates;

use App\Models\User;
use Illuminate\Database\Seeder;

class CreateDefaultBookmarkSeeder extends Seeder
{
    /**
     * ブックマークが無いユーザーにデフォルトのブックマークを追加する.
     */
    public function run()
    {
        foreach (User::withTrashed()->cursor() as $user) {
            if ($user->bookmarks()->count() === 0) {
                $user->bookmarks()->create(['title' => 'ブックマーク']);
            }
        }
    }
}
