<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ArticlePostType;
use App\Enums\UserRole;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * 開発環境用シーダー
 */
class DevSeeder extends Seeder
{
    /**
     * テスト用一般ユーザーと記事を作成する.
     */
    public function run(): void
    {
        Attachment::where('id', '<>', null)->delete();
        User::where('role', UserRole::User)->delete();

        User::factory()->count(20)->create()->each(function ($user): void {
            $user->articles()->saveMany(
                Article::factory()->count(random_int(0, 20))->make()
            );
        });

        // add attachment into article
        foreach (User::with(['articles', 'profile'])->cursor() as $user) {
            $this->addAvatar($user);

            foreach ($user->articles as $article) {
                // アドオン投稿
                if ($article->post_type === ArticlePostType::AddonPost) {
                    $this->addAddonPost($user, $article);
                    $this->addCategories($article);
                    $this->addTags($article);
                }

                // アドオン紹介
                if ($article->post_type === ArticlePostType::AddonIntroduction) {
                    $this->addAddonIntroduction($user, $article);
                    $this->addCategories($article);
                    $this->addTags($article);
                }
            }
        }
    }

    private function addAvatar($user): void
    {
        $avatar = Attachment::make([
            'user_id' => $user->id,
            'original_name' => $user->name.'のアバター.png',
            'path' => 'default/avatar.png',
        ]);
        $user->profile->attachments()->save($avatar);
    }

    private function addAddonPost($user, $article): void
    {
        // add attachments
        $thumb = Attachment::make([
            'user_id' => $user->id,
            'original_name' => 'アドオン投稿「'.$article->title.'」のサムネイル.png',
            'path' => 'default/sample'.random_int(0, 2).'.png',
        ]);
        $file = Attachment::make([
            'user_id' => $user->id,
            'original_name' => 'アドオン投稿「'.$article->title.'」のzipファイル.zip',
            'path' => 'default/sample.zip',
        ]);
        $article->attachments()->saveMany([$thumb, $file]);

        // update contents
        $c = $article->contents;
        $c->file = $file->id;
        $c->thumbnail = $thumb->id;
        $c->thanks = '圧倒的感謝';
        $c->license = '改造自由';
        $article->contents = $c;
        $article->save();
    }

    private function addAddonIntroduction($user, $article): void
    {
        // add attachments
        $thumb = Attachment::make([
            'user_id' => $user->id,
            'original_name' => 'アドオン紹介「'.$article->title.'」のサムネイル.png',
            'path' => 'default/sample'.random_int(0, 2).'.png',
        ]);
        $article->attachments()->saveMany([$thumb]);

        // update contents
        $c = $article->contents;
        $c->link = 'http://example.com';
        $c->thumbnail = $thumb->id;
        $c->thanks = '圧倒的感謝';
        $article->contents = $c;
        $article->save();
    }

    private function addCategories($article): void
    {
        $ids = collect([]);
        $ids = $ids->merge(Category::pak()->inRandomOrder()->limit(random_int(1, 3))->get()->pluck('id'));
        $ids = $ids->merge(Category::addon()->inRandomOrder()->limit(random_int(1, 10))->get()->pluck('id'));
        $ids = $ids->merge(Category::pak128Position()->inRandomOrder()->limit(random_int(0, 1))->get()->pluck('id'));
        $ids = $ids->merge(Category::license()->inRandomOrder()->limit(random_int(0, 1))->get()->pluck('id'));
        $article->categories()->sync($ids);
    }

    private function addTags($article): void
    {
        $tags = Tag::factory()->count(random_int(0, 10))->make()->map(fn ($tag) => Tag::firstOrCreate(['name' => $tag->name]));
        $article->tags()->sync($tags->pluck('id'));
    }
}
