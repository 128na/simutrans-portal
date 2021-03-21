<?php

namespace Tests;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;

abstract class ArticleTestCase extends TestCase
{
    protected Attachment $user_file;
    protected User $user2;
    protected Article $article2;
    protected Attachment $user2_image;
    protected Attachment $user2_file;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user_file = Attachment::createFromFile(UploadedFile::fake()->create('other.zip', 1), $this->user->id);
        $this->user2 = User::factory()->create();
        $this->article2 = Article::factory()->create(['user_id' => $this->user2->id]);
        $this->user2_image = Attachment::createFromFile(UploadedFile::fake()->image('other.png', 1), $this->user2->id);
        $this->user2_file = Attachment::createFromFile(UploadedFile::fake()->image('other.zip', 1), $this->user2->id);
    }

    /**
     * 記事投稿共通バリデーション.
     */
    public function dataStoreArticleValidation()
    {
        yield '投稿形式が空' => [fn () => ['post_type' => ''], 'article.post_type'];
        yield '不正な投稿形式' => [fn () => ['post_type' => 'test_example'], 'article.post_type'];
    }

    /**
     * 記事投稿・更新共通バリデーション.
     */
    public function dataArticleValidation()
    {
        yield 'ステータスが空' => [fn () => ['status' => ''], 'article.status'];
        yield '不正なステータス' => [fn () => ['status' => 'test_example'], 'article.status'];

        yield 'タイトルが空' => [fn () => ['title' => ''], 'article.title'];
        yield 'タイトルが256文字以上' => [fn () => ['title' => str_repeat('a', 256)], 'article.title'];
        yield 'タイトルが重複' => [fn () => ['title' => $this->article2->title], 'article.title'];

        yield 'スラッグが空' => [fn () => ['slug' => ''], 'article.slug'];
        yield 'スラッグが256文字以上' => [fn () => ['slug' => str_repeat('a', 256)], 'article.slug'];
        yield 'スラッグが重複' => [fn () => ['slug' => $this->article2->slug], 'article.slug'];

        yield '存在しないサムネイルID' => [fn () => ['contents' => ['thumbnail' => 99999]], 'article.contents.thumbnail'];
        yield '画像以外' => [fn () => ['contents' => ['thumbnail' => $this->user_file->id]], 'article.contents.thumbnail'];
        yield '他人の投稿したサムネイルID' => [fn () => ['contents' => ['thumbnail' => $this->user2_image->id]], 'article.contents.thumbnail'];

        yield 'タグ名が空' => [fn () => ['tags' => null], 'article.tags'];
        yield '存在しないタグ名' => [fn () => ['tags' => ['missing_tag']], 'article.tags.0'];
        yield 'タグ名が256文字以上' => [fn () => ['tags' => [str_repeat('a', 256)]], 'article.tags.0'];
        yield 'カテゴリが空' => [fn () => ['categories' => null], 'article.categories'];
        yield '存在しないカテゴリ' => [fn () => ['categories' => [99999]], 'article.categories.0'];
        yield 'OK' => [fn () => [], null];
    }

    public function dataAddonValidation()
    {
        yield '説明が空' => [fn () => ['contents' => ['description' => '']], 'article.contents.description'];
        yield '説明が2049文字以上' => [fn () => ['contents' => ['description' => str_repeat('a', 2049)]], 'article.contents.description'];
        yield '謝辞が2049文字以上' => [fn () => ['contents' => ['thanks' => str_repeat('a', 2049)]], 'article.contents.thanks'];
        yield 'ライセンス（その他）が2049文字以上' => [fn () => ['contents' => ['license' => str_repeat('a', 2049)]], 'article.contents.license'];
    }

    public function dataAddonIntroductionValidation()
    {
        yield 'アドオン作者が空' => [fn () => ['contents' => ['author' => '']], 'article.contents.author'];
        yield 'アドオン作者が256文字以上' => [fn () => ['contents' => ['author' => str_repeat('a', 256)]], 'article.contents.author'];
        yield 'リンクが空' => [fn () => ['contents' => ['link' => '']], 'article.contents.link'];
        yield 'リンクが不正なURL' => [fn () => ['contents' => ['link' => 'not_url']], 'article.contents.link'];
    }

    public function dataAddonPostValidation()
    {
        yield 'ファイルIDが空' => [fn () => ['contents' => ['file' => '']], 'article.contents.file'];
        yield '存在しないファイルID' => [fn () => ['contents' => ['file' => 99999]], 'article.contents.file'];
        yield '他人の投稿したファイルID' => [fn () => ['contents' => ['file' => $this->user2_file->id]], 'article.contents.file'];
    }
}
