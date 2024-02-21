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
     *
     * @return void
     */
    public function run()
    {
        $admin = self::addAdminUser();
        self::addItems(config('category.pak'));
        self::addItems(config('category.addon'));
        self::addItems(config('category.pak128_position'));
        self::addItems(config('category.license'));
        self::addItems(config('category.page'));
    }

    private static function addAdminUser()
    {
        if (is_null(config('admin.email'))) {
            throw new \Exception('admin email was empty!');
        }

        $admin = User::firstOrCreate(
            ['role' => config('role.admin'), 'name' => config('admin.name'), 'email' => config('admin.email')],
            ['password' => bcrypt(config('admin.password')), 'email_verified_at' => now()]
        );

        return $admin;
    }

    private static function addItems($items)
    {
        return collect($items)->map(fn ($item) => Category::firstOrCreate($item));
    }
}
