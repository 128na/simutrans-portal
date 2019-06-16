<?php

use Illuminate\Database\Seeder;
use App\Models\Attachment;
use App\Models\Article;
use App\Models\Category;
use App\Models\Profile;
use App\Models\User;

/**
 * 開発環境用シーダー
 */
class DevSeeder extends Seeder
{
    /**
     * テスト用一般ユーザーと記事を作成する
     *
     * @return void
     */
    public function run()
    {
        User::where('role', config('role.user'))->delete();

        factory(User::class, 10)->create()->each(function ($user) {
            $user->profile()->save(
                factory(Profile::class)->make()
            );
            $user->articles()->saveMany(
                factory(Article::class, random_int(0, 5))->make()
            );
        });

        $category_post = Category::where('slug', 'addon-post')->first();
        $category_introduction = Category::where('slug', 'addon-introduction')->first();

        // add attachment into article
        foreach(User::with(['articles', 'profile'])->cursor() as $user) {
            self::addAvater($user);

            foreach($user->articles as $article) {
                // アドオン投稿
                if(random_int(0,1)) {
                    self::addAddonPost($user, $article);
                    self::AddCategories($article, $category_post);
                }
                // アドオン紹介
                else {
                    self::addAddonIntroduction($user, $article);
                    self::AddCategories($article, $category_introduction);
                }
            }
        }
    }

    private static function addAvater($user)
    {
        $avater = Attachment::make([
            'user_id'       => $user->id,
            'original_name' => $user->name.'のアバター.jpg',
            'path'          => 'avater.jpg',
        ]);
        $user->profile->attachments()->save($avater);
    }

    private static function addAddonPost($user, $article)
    {
        // add attachments
        $thumb = Attachment::make([
            'user_id' => $user->id,
            'original_name' => 'アドオン投稿「'.$article->title.'」のサムネイル.png',
            'path' => 'sample'.random_int(0,2).'.png',
        ]);
        $file = Attachment::make([
            'user_id' => $user->id,
            'original_name' => 'アドオン投稿「'.$article->title.'」のzipファイル.zip',
            'path' => 'sample.zip',
        ]);
        $article->attachments()->saveMany([$thumb, $file]);

        // update contents
        $c = $article->contents;
        $c['file']      = $file->id;
        $c['thumbnail'] = $thumb->id;
        $c['thanks']    = '圧倒的感謝';
        $c['license']   = 'Creative Commons';
        $article->contents = $c;
        $article->save();
    }

    private static function addAddonIntroduction($user, $article)
    {
        // add attachments
        $thumb = Attachment::make([
            'user_id' => $user->id,
            'original_name' => 'アドオン紹介「'.$article->title.'」のサムネイル.png',
            'path' => 'sample'.random_int(0,2).'.png',
        ]);
        $article->attachments()->saveMany([$thumb]);

        // update contents
        $c = $article->contents;
        $c['link']      = 'http://example.com';
        $c['thumbnail'] = $thumb->id;
        $c['thanks']    = '圧倒的感謝';
        $c['license']   = 'MIT';
        $article->contents = $c;
        $article->save();
    }

    private static function addCategories($article, $type)
    {
        $ids = collect([$type->id]);
        $ids = $ids->merge(Category::pak()->inRandomOrder()->limit(random_int(1, 3))->get()->pluck('id'));
        $ids = $ids->merge(Category::addon()->inRandomOrder()->limit(random_int(1, 5))->get()->pluck('id'));
        $ids = $ids->merge(Category::pak128Position()->inRandomOrder()->limit(random_int(0, 1))->get()->pluck('id'));
        $article->categories()->sync($ids);
    }
}
