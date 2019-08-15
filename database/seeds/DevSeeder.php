<?php

use Illuminate\Database\Seeder;
use App\Models\Attachment;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
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
        Attachment::where('id', '<>', null)->delete();
        User::where('role', config('role.user'))->delete();

        factory(User::class, 20)->create()->each(function ($user) {
            $user->articles()->saveMany(
                factory(Article::class, random_int(0, 20))->make()
            );
        });

        // add attachment into article
        foreach(User::with(['articles', 'profile'])->cursor() as $user) {
            self::addAvatar($user);

            foreach($user->articles as $article) {
                // アドオン投稿
                if($article->post_type === 'addon-post') {
                    self::addAddonPost($user, $article);
                    self::addCategories($article);
                    self::addTags($article);
                }
                // アドオン紹介
                if($article->post_type === 'addon-introduction') {
                    self::addAddonIntroduction($user, $article);
                    self::addCategories($article);
                    self::addTags($article);
                }
                // 一般記事
                if($article->post_type === 'page') {
                }
            }
        }
    }

    private static function addAvatar($user)
    {
        $avatar = Attachment::make([
            'user_id'       => $user->id,
            'original_name' => $user->name.'のアバター.png',
            'path'          => 'default/avatar.png',
        ]);
        $user->profile->attachments()->save($avatar);
    }

    private static function addAddonPost($user, $article)
    {
        // add attachments
        $thumb = Attachment::make([
            'user_id' => $user->id,
            'original_name' => 'アドオン投稿「'.$article->title.'」のサムネイル.png',
            'path' => 'default/sample'.random_int(0,2).'.png',
        ]);
        $file = Attachment::make([
            'user_id' => $user->id,
            'original_name' => 'アドオン投稿「'.$article->title.'」のzipファイル.zip',
            'path' => 'default/sample.zip',
        ]);
        $article->attachments()->saveMany([$thumb, $file]);

        // update contents
        $c = $article->contents;
        $c->file      = $file->id;
        $c->thumbnail = $thumb->id;
        $c->thanks    = '圧倒的感謝';
        $c->license   = '改造自由';
        $article->contents = $c;
        $article->save();
    }

    private static function addAddonIntroduction($user, $article)
    {
        // add attachments
        $thumb = Attachment::make([
            'user_id' => $user->id,
            'original_name' => 'アドオン紹介「'.$article->title.'」のサムネイル.png',
            'path' => 'default/sample'.random_int(0,2).'.png',
        ]);
        $article->attachments()->saveMany([$thumb]);

        // update contents
        $c = $article->contents;
        $c->link      = 'http://example.com';
        $c->thumbnail = $thumb->id;
        $c->thanks    = '圧倒的感謝';
        $article->contents = $c;
        $article->save();
    }

    private static function addCategories($article)
    {
        $ids = collect([]);
        $ids = $ids->merge(Category::pak()->inRandomOrder()->limit(random_int(1, 3))->get()->pluck('id'));
        $ids = $ids->merge(Category::addon()->inRandomOrder()->limit(random_int(1, 10))->get()->pluck('id'));
        $ids = $ids->merge(Category::pak128Position()->inRandomOrder()->limit(random_int(0, 1))->get()->pluck('id'));
        $ids = $ids->merge(Category::license()->inRandomOrder()->limit(random_int(0, 1))->get()->pluck('id'));
        $article->categories()->sync($ids);
    }

    private static function addTags($article)
    {
        $tags = factory(Tag::class, random_int(0, 10))->make()->map(function ($tag) {
            return Tag::firstOrCreate(['name' => $tag->name]);
        });
        $article->tags()->sync($tags->pluck('id'));
    }
}
