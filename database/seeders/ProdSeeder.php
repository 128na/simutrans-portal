<?php
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
     * 管理者とカテゴリを追加する
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
        self::addAnounces($admin);
    }

    private static function addAdminUser()
    {
        if (is_null(config('admin.email'))) {
            throw new \Exception("admin email was empty!");
        }

        $admin = User::firstOrCreate(
            ['role' => config('role.admin'), 'name' => config('admin.name'), 'email' => config('admin.email')],
            ['password' => bcrypt(config('admin.password')), 'email_verified_at' => now()]
        );

        return $admin;
    }

    private static function addItems($items)
    {
        return collect($items)->map(function ($item) {
            return Category::firstOrCreate($item);
        });
    }

    /**
     * お知らせ記事作成
     */
    private static function addAnounces($user)
    {
        $announce_category = Category::page()->slug('announce')->firstOrFail();

        foreach (config('announces', []) as $data) {
            $data = array_merge([
                'post_type' => config('post_types.page'),
                'status' => config('status.publish'),
            ], $data);

            $article = $user->articles()->updateOrCreate(['slug' => $data['slug']], $data);
            $article->categories()->sync($announce_category->id);
        }
    }
}
