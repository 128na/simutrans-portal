<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
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
        $this->addAdminUser();
        $this->addItems(config('category.pak'));
        $this->addItems(config('category.addon'));
        $this->addItems(config('category.pak128_position'));
        $this->addItems(config('category.license'));
        $this->addItems(config('category.page'));
    }

    private function addAdminUser()
    {
        if (is_null(env('ADMIN_EMAIL'))) {
            throw new \Exception('env ADMIN_EMAIL is empty or cached!');
        }

        return User::firstOrCreate(
            ['role' => config('role.admin'), 'name' => env('ADMIN_NAME'), 'email' => env('ADMIN_EMAIL')],
            ['password' => bcrypt(env('ADMIN_PASSWORD')), 'email_verified_at' => now()]
        );
    }

    private function addItems($items)
    {
        return collect($items)->map(fn ($item) => Category::firstOrCreate($item));
    }
}
