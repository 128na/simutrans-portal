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
        self::addPostCategories();
        self::addPakCategories();
        self::addAddonCategories();
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

    private static function addPostCategories()
    {
        return collect(
            [
                ['slug' => 'addon-post',         'type' => config('category.type.post'), 'order' => 00000, 'name' => 'アドオン投稿'],
                ['slug' => 'addon-introduction', 'type' => config('category.type.post'), 'order' => 00010, 'name' => 'アドオン紹介'],
            ])->map(function($post) {
                return Category::firstOrCreate($post);
        });
    }

    private static function addPakCategories()
    {
        return collect(
            [
                ['slug' => '64',           'type' => config('category.type.pak'), 'order' => 10000, 'name' => 'pak64'],
                ['slug' => '128',          'type' => config('category.type.pak'), 'order' => 10010, 'name' => 'pak128'],
                ['slug' => '128-japan',    'type' => config('category.type.pak'), 'order' => 10011, 'name' => 'pak128.Japan'],
                ['slug' => '256',          'type' => config('category.type.pak'), 'order' => 10020, 'name' => 'pak256'],
                ['slug' => '96-comic',     'type' => config('category.type.pak'), 'order' => 10030, 'name' => 'pak96.Comic'],
                ['slug' => '192-comic',    'type' => config('category.type.pak'), 'order' => 10031, 'name' => 'pak192.Comic'],
                ['slug' => '256-extended', 'type' => config('category.type.pak'), 'order' => 10040, 'name' => 'pak256(Extended)'],
            ])->map(function($pak) {
                return Category::firstOrCreate($pak);
        });
    }

    private static function addAddonCategories()
    {
        return collect(
            [
                ['slug' => 'trains',               'type' => config('category.type.addon'), 'order' => 20010, 'name'=> '鉄道車両'],
                ['slug' => 'rail-tools',           'type' => config('category.type.addon'), 'order' => 20020, 'name'=> '鉄道施設'],
                ['slug' => 'road-tools',           'type' => config('category.type.addon'), 'order' => 20030, 'name'=> '道路施設'],
                ['slug' => 'ships',                'type' => config('category.type.addon'), 'order' => 20040, 'name'=> '船舶'],
                ['slug' => 'aircrafts',            'type' => config('category.type.addon'), 'order' => 20050, 'name'=> '航空機'],
                ['slug' => 'road-vehicles',        'type' => config('category.type.addon'), 'order' => 20060, 'name'=> '自動車'],
                ['slug' => 'airport-tools',        'type' => config('category.type.addon'), 'order' => 20070, 'name'=> '空港施設'],
                ['slug' => 'industrial-tools',     'type' => config('category.type.addon'), 'order' => 20080, 'name'=> '産業'],
                ['slug' => 'seaport-tools',        'type' => config('category.type.addon'), 'order' => 20090, 'name'=> '港湾施設'],
                ['slug' => 'buildings',            'type' => config('category.type.addon'), 'order' => 20100, 'name'=> '一般建築物'],
                ['slug' => 'monorail-vehicles',    'type' => config('category.type.addon'), 'order' => 20110, 'name'=> 'モノレール車両'],
                ['slug' => 'monorail-tools',       'type' => config('category.type.addon'), 'order' => 20120, 'name'=> 'モノレール施設'],
                ['slug' => 'maglev-vehicles',      'type' => config('category.type.addon'), 'order' => 20130, 'name'=> 'マグレブ(リニア)車両'],
                ['slug' => 'maglev-tools',         'type' => config('category.type.addon'), 'order' => 20140, 'name'=> 'マグレブ(リニア)施設'],
                ['slug' => 'narrow-gauge-vahicle', 'type' => config('category.type.addon'), 'order' => 20150, 'name'=> 'ナローゲージ車両'],
                ['slug' => 'narrow-gauge-tools',   'type' => config('category.type.addon'), 'order' => 20160, 'name'=> 'ナローゲージ施設'],
                ['slug' => 'tram-vehicle',         'type' => config('category.type.addon'), 'order' => 20170, 'name'=> 'トラム車両'],
                ['slug' => 'tram-tools',           'type' => config('category.type.addon'), 'order' => 20180, 'name'=> 'トラム施設'],
                ['slug' => 'others',               'type' => config('category.type.addon'), 'order' => 29000, 'name'=> 'その他'],
            ])->map(function($addon) {
                return Category::firstOrCreate($addon);
        });
    }

    private static function addPak128PositionCategories()
    {
        return collect(
            [
                ['slug' => 'old', 'type' => config('category.type.pak128_position'), 'order' => 30000, 'name' => '旧描画位置（主に112.3以前）'],
                ['slug' => 'new', 'type' => config('category.type.pak128_position'), 'order' => 30010, 'name' => '新描画位置（主に120.0以降）'],
            ])->map(function($position) {
                return Category::firstOrCreate($position);
        });
    }
}
