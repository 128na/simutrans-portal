<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Profile;

/**
 * 本番環境用シーダー
 */
class ProdSeeder extends Seeder
{
    /**
     * 管理者とカテゴリを追加する
     *
     * @return void
     */
    public function run()
    {
        self::addAdminUser();
        self::addItems(config('category.post'));
        self::addItems(config('category.pak'));
        self::addItems(config('category.addon'));
        self::addItems(config('category.pak128_position'));
        self::addItems(config('category.license'));
    }

    private static function addAdminUser()
    {
        $admin = User::with('profile')->firstOrCreate(
            ['role' => config('role.admin'), 'name' => config('admin.name'), 'email' => config('admin.email')],
            ['password' => bcrypt(config('admin.password'))]
        );

        if(!$admin->profile) {
            $admin->profile()->save(factory(Profile::class)->make());
        }
    }

    private static function addItems($items)
    {
        return collect($items)->map(function($item) {
            return Category::firstOrCreate($item);
        });
    }
}
