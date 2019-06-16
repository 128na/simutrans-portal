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
        $pak = Category::firstOrCreate(['slug' => 'pak', 'name' => 'pakセット',]);

        $paks = collect(
            [
                ['slug' => '64',           'name' => 'pak64'],
                ['slug' => '128',          'name' => 'pak128'],
                ['slug' => '128-japan',    'name' => 'pak128.Japan'],
                ['slug' => '256',          'name' => 'pak256'],
                ['slug' => '256-extended', 'name' => 'pak256(Extended)'],
            ])->map(function($pak) {
                return Category::firstOrCreate($pak);
        });
        $pak->children()->saveMany($paks);
    }

    private static function addTypeCategories()
    {
        $type = Category::firstOrCreate(['slug' => 'type', 'name' => '種類']);

        $types = collect(
            [
                ['slug' => 'others',               'name'=> 'その他'],
                ['slug' => 'tram-tools',           'name'=> 'トラム施設'],
                ['slug' => 'tram-vehicle',         'name'=> 'トラム車両'],
                ['slug' => 'narrow-gauge-tools',   'name'=> 'ナローゲージ施設'],
                ['slug' => 'narrow-gauge-vahicle', 'name'=> 'ナローゲージ車両'],
                ['slug' => 'maglev-tools',         'name'=> 'マグレブ(リニア)施設'],
                ['slug' => 'maglev-vehicles',      'name'=> 'マグレブ(リニア)車両'],
                ['slug' => 'monorail-tools',       'name'=> 'モノレール施設'],
                ['slug' => 'monorail-vehicles',    'name'=> 'モノレール車両'],
                ['slug' => 'buildings',            'name'=> '一般建築物'],
                ['slug' => 'seaport-tools',        'name'=> '港湾施設'],
                ['slug' => 'industrial-tools',     'name'=> '産業'],
                ['slug' => 'airport-tools',        'name'=> '空港施設'],
                ['slug' => 'road-vehicles',        'name'=> '自動車'],
                ['slug' => 'aircrafts',            'name'=> '航空機'],
                ['slug' => 'ships',                'name'=> '船舶'],
                ['slug' => 'road-tools',           'name'=> '道路施設'],
                ['slug' => 'rail-tools',           'name'=> '鉄道施設'],
                ['slug' => 'trains',               'name'=> '鉄道車両'],

            ])->map(function($type) {
                return Category::firstOrCreate($type);
        });
        $type->children()->saveMany($types);
    }

    private static function addPak128PositionCategories()
    {
        $position = Category::firstOrCreate(['slug' => 'pak128-position', 'name' => 'pak128用描画位置']);

        $positions = collect(
            [
                ['slug' => 'new', 'name' => '新描画位置（主に120.0以降）'],
                ['slug' => 'old', 'name' => '旧描画位置（主に112.3以前）'],
            ])->map(function($position) {
                return Category::firstOrCreate($position);
        });
        $position->children()->saveMany($positions);
    }

    private static function addPostTypeCategories()
    {
        $post_type = Category::firstOrCreate(['slug' => 'post-type', 'name' => '投稿形式']);

        $post_types = collect(
            [
                ['slug' => 'addon-post',         'name' => 'アドオン投稿'],
                ['slug' => 'addon-introduction', 'name' => 'アドオン紹介'],
            ])->map(function($post_type) {
                return Category::firstOrCreate($post_type);
        });
        $post_type->children()->saveMany($post_types);
    }
}
