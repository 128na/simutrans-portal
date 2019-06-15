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
        self::addPostTypeCategories();
        self::addPakCategories();
        self::addTypeCategories();
        self::addPak128PositionCategories();
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

    private static function addPakCategories()
    {
        $pak = Category::firstOrCreate(['name' => 'pakセット', 'slug' => 'pak']);

        $paks = collect(
            [
                ['name' => '64', 'slug' => '64'],
                ['name' => '128', 'slug' => '128'],
                ['name' => '128.japan', 'slug' => '128-japan'],
                ['name' => '256(Extended)', 'slug' => '256-extended'],
            ])->map(function($pak) {
                return Category::firstOrCreate($pak);
        });
        $pak->children()->saveMany($paks);
    }

    private static function addTypeCategories()
    {
        $type = Category::firstOrCreate(['name' => '種類', 'slug' => 'type']);

        $types = collect(
            [
                ['name'=> 'その他', 'slug' => 'others'],
                ['name'=> 'トラム施設', 'slug' => 'tram-tools'],
                ['name'=> 'トラム車両', 'slug' => 'tram-vehicle'],
                ['name'=> 'ナローゲージ施設', 'slug' => 'narrow-gauge-tools'],
                ['name'=> 'ナローゲージ車両', 'slug' => 'narrow-gauge-vahicle'],
                ['name'=> 'マグレブ(リニア)施設', 'slug' => 'maglev-tools'],
                ['name'=> 'マグレブ(リニア)車両', 'slug' => 'maglev-vehicles'],
                ['name'=> 'モノレール施設', 'slug' => 'monorail-tools'],
                ['name'=> 'モノレール車両', 'slug' => 'monorail-vehicles'],
                ['name'=> '一般建築物', 'slug' => 'buildings'],
                ['name'=> '港湾施設', 'slug' => 'seaport-tools'],
                ['name'=> '産業', 'slug' => 'industrial-tools'],
                ['name'=> '空港施設', 'slug' => 'airport-tools'],
                ['name'=> '自動車', 'slug' => 'road-vehicles'],
                ['name'=> '航空機', 'slug' => 'aircrafts'],
                ['name'=> '船舶', 'slug' => 'ships'],
                ['name'=> '道路施設', 'slug' => 'road-tools'],
                ['name'=> '鉄道施設', 'slug' => 'rail-tools'],
                ['name'=> '鉄道車両', 'slug' => 'trains'],

            ])->map(function($type) {
                return Category::firstOrCreate($type);
        });
        $type->children()->saveMany($types);
    }

    private static function addPak128PositionCategories()
    {
        $position = Category::firstOrCreate(['name' => 'pak128用描画位置', 'slug' => 'pak128-position']);

        $positions = collect(
            [
                ['name' => '新描画位置（主に120.0以降）', 'slug' => 'new'],
                ['name' => '旧描画位置（主に112.3以前）', 'slug' => 'old'],
            ])->map(function($position) {
                return Category::firstOrCreate($position);
        });
        $position->children()->saveMany($positions);
    }

    private static function addPostTypeCategories()
    {
        $post_type = Category::firstOrCreate(['name' => '投稿形式', 'slug' => 'post-type']);

        $post_types = collect(
            [
                ['name' => 'アドオン投稿', 'slug' => 'addon-post'],
                ['name' => 'アドオン紹介', 'slug' => 'addon-introduction'],
            ])->map(function($post_type) {
                return Category::firstOrCreate($post_type);
        });
        $post_type->children()->saveMany($post_types);
    }
}
