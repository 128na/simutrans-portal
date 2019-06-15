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

        $category_post = Category::firstOrCreate(['slug' => 'addon-post']);
        $category_introduction = Category::firstOrCreate(['slug' => 'addon-introduction']);

        // add attachment into article
        foreach(User::with(['articles', 'profile'])->cursor() as $user) {
            self::addAvater($user);

            foreach($user->articles as $article) {
                // アドオン投稿
                if(random_int(0,1)) {
                    self::addAddonPost($user, $article, $category_post);
                }
                // アドオン紹介
                else {
                    self::addAddonIntroduction($user, $article, $category_introduction);
                }
            }
        }
    }


    private static function addAvater($user)
    {
        $avater = Attachment::make([
            'user_id' => $user->id,
            'original_name' => $user->name.'のアバター',
            'path' => 'avater.png',
        ]);
        $user->profile->attachments()->save($avater);
    }

    private static function addAddonPost($user, $article, $category)
    {
        // add attachments
        $thumb = Attachment::make([
            'user_id' => $user->id,
            'original_name' => 'アドオン投稿「'.$article->title.'」のサムネイル',
            'path' => 'sample.png',
        ]);
        $file = Attachment::make([
            'user_id' => $user->id,
            'original_name' => 'アドオン投稿「'.$article->title.'」のzipファイル',
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

        // add categories
        $category_ids = [$category->id];
        $category_ids[] = Category::where('parent_id', '<>', $category->parent_id)->inRandomOrder()->first()->id;
        $article->categories()->sync($category_ids);
    }

    private static function addAddonIntroduction($user, $article, $category)
    {
        // add attachments
        $thumb = Attachment::make([
            'user_id' => $user->id,
            'original_name' => 'アドオン紹介「'.$article->title.'」のサムネイル',
            'path' => 'sample.png',
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

        // add categories
        $category_ids = [$category->id];
        $category_ids[] = Category::where('parent_id', '<>', $category->parent_id)->inRandomOrder()->first()->id;
        $article->categories()->sync($category_ids);
    }
}
