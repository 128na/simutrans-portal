<?php

declare(strict_types=1);

namespace Tests;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\UploadedFile;

abstract class ArticleTestCase extends TestCase
{
    /**
     * 一般ユーザーの公開記事.
     */
    protected Article $article;

    protected Attachment $user_file;

    protected User $user2;

    protected Article $article2;

    protected Attachment $user2_image;

    protected Attachment $user2_file;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article = $this->createAddonIntroduction();
        $this->user_file = $this->createFromFile(UploadedFile::fake()->create('other.zip', 1), $this->user->id);
        $this->user2 = User::factory()->create();
        $this->article2 = $this->createAddonIntroduction($this->user2);
        $this->user2_image = $this->createFromFile(UploadedFile::fake()->image('other.png', 1), $this->user2->id);
        $this->user2_file = $this->createFromFile(UploadedFile::fake()->image('other.zip', 1), $this->user2->id);
    }

    protected function createFromFile(UploadedFile $uploadedFile, int $userId): Attachment
    {
        return Attachment::create([
            'user_id' => $userId,
            'path' => $uploadedFile->store('user/'.$userId, 'public'),
            'original_name' => $uploadedFile->getClientOriginalName(),
        ]);
    }

    protected function createAddonPost($user = null)
    {
        $user ??= $this->user;
        $file = UploadedFile::fake()->create('file.zip', 1, 'application/zip');
        $attachment = $this->createFromFile($file, $user->id);
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'post_type' => ArticlePostType::AddonPost,
            'status' => ArticleStatus::Publish,
            'title' => 'test_addon-post'.random_int(1, 999),
            'contents' => [
                'description' => 'test addon-post text'.random_int(1, 999),
                'author' => 'test author',
                'file' => $attachment->id,
            ],
        ]);
        $article->attachments()->save($attachment);

        return $article;
    }

    protected function createAddonIntroduction($user = null)
    {
        $user ??= $this->user;

        return Article::factory()->create([
            'user_id' => $user->id,
            'post_type' => ArticlePostType::AddonIntroduction,
            'status' => ArticleStatus::Publish,
            'title' => 'test_addon-introduction'.random_int(1, 999),
            'contents' => [
                'description' => 'test addon-introduction text'.random_int(1, 999),
                'author' => 'test author',
                'link' => 'http://example.com',
            ],
        ]);
    }

    protected function createPage($user = null)
    {
        $user ??= $this->user;

        return Article::factory()->create([
            'user_id' => $user->id,
            'post_type' => ArticlePostType::Page,
            'status' => ArticleStatus::Publish,
            'title' => 'test_page',
            'contents' => [
                'sections' => [['type' => 'text', 'text' => 'test page text']],
            ],
        ]);
    }

    protected function createMarkdown($user = null)
    {
        $user ??= $this->user;

        return Article::factory()->create([
            'user_id' => $user->id,
            'post_type' => ArticlePostType::Markdown,
            'status' => ArticleStatus::Publish,
            'title' => 'test_markdown',
            'contents' => [
                'markdown' => '# test markdown text',
            ],
        ]);
    }

    protected function createAnnounce($user = null)
    {
        $user ??= $this->user;
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'post_type' => ArticlePostType::Page,
            'status' => ArticleStatus::Publish,
            'title' => 'test_announce',
            'contents' => [
                'sections' => [['type' => 'text', 'text' => 'test announce text']],
            ],
        ]);
        $announce_category = Category::page()->slug('announce')->firstOrFail();
        $article->categories()->save($announce_category);

        return $article;
    }

    protected function createMarkdownAnnounce($user = null)
    {
        $user ??= $this->user;
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'post_type' => ArticlePostType::Markdown,
            'status' => ArticleStatus::Publish,
            'title' => 'test_markdown',
            'contents' => [
                'markdown' => '# test markdown text',
            ],
        ]);
        $announce_category = Category::page()->slug('announce')->firstOrFail();
        $article->categories()->save($announce_category);

        return $article;
    }

    /**
     * 記事投稿共通バリデーション.
     */
    public static function dataStoreArticleValidation(): \Generator
    {
        yield '投稿形式が空' => [fn (): array => ['post_type' => ''], 'article.post_type'];
        yield '不正な投稿形式' => [fn (): array => ['post_type' => 'test_example'], 'article.post_type'];
    }

    /**
     * 記事投稿・更新共通バリデーション.
     */
    public static function dataArticleValidation(): \Generator
    {
        yield 'ステータスが空' => [fn (): array => ['status' => ''], 'article.status'];
        yield '不正なステータス' => [fn (): array => ['status' => 'test_example'], 'article.status'];

        yield 'タイトルが空' => [fn (): array => ['title' => ''], 'article.title'];
        yield 'タイトルが256文字以上' => [fn (): array => ['title' => str_repeat('a', 256)], 'article.title'];
        yield 'タイトルが重複' => [fn (): array => ['title' => $this->article2->title], 'article.title'];
        yield 'タイトルにNG文字' => [fn (): array => ['title' => '@example'], 'article.title'];

        yield 'スラッグが空' => [fn (): array => ['slug' => ''], 'article.slug'];
        yield 'スラッグが256文字以上' => [fn (): array => ['slug' => str_repeat('a', 256)], 'article.slug'];

        yield '存在しないサムネイルID' => [fn (): array => ['contents' => ['thumbnail' => 99999]], 'article.contents.thumbnail'];
        yield '画像以外' => [fn (): array => ['contents' => ['thumbnail' => $this->user_file->id]], 'article.contents.thumbnail'];
        yield '他人の投稿したサムネイルID' => [fn (): array => ['contents' => ['thumbnail' => $this->user2_image->id]], 'article.contents.thumbnail'];

        yield 'カテゴリが空' => [fn (): array => ['categories' => null], 'article.categories'];
        yield '存在しないカテゴリ' => [fn (): array => ['categories' => [['id' => 99999]]], 'article.categories.0.id'];
        yield 'OK' => [fn (): array => [], null];
    }

    /**
     * アドオン形式の追加項目.
     */
    public static function dataAddonValidation(): \Generator
    {
        yield 'タグ名が空' => [fn (): array => ['tags' => null], 'article.tags'];
        yield '存在しないタグ' => [fn (): array => ['tags' => [['id' => -1]]], 'article.tags.0.id'];
        yield '説明が空' => [fn (): array => ['contents' => ['description' => '']], 'article.contents.description'];
        yield '説明が2049文字以上' => [fn (): array => ['contents' => ['description' => str_repeat('a', 2049)]], 'article.contents.description'];
        yield '謝辞が2049文字以上' => [fn (): array => ['contents' => ['thanks' => str_repeat('a', 2049)]], 'article.contents.thanks'];
        yield 'ライセンス（その他）が2049文字以上' => [fn (): array => ['contents' => ['license' => str_repeat('a', 2049)]], 'article.contents.license'];
    }

    /**
     * アドオン紹介の追加項目.
     */
    public static function dataAddonIntroductionValidation(): \Generator
    {
        yield 'アドオン作者が空' => [fn (): array => ['contents' => ['author' => '']], 'article.contents.author'];
        yield 'アドオン作者が256文字以上' => [fn (): array => ['contents' => ['author' => str_repeat('a', 256)]], 'article.contents.author'];
        yield 'リンクが空' => [fn (): array => ['contents' => ['link' => '']], 'article.contents.link'];
        yield 'リンクが不正なURL' => [fn (): array => ['contents' => ['link' => 'not_url']], 'article.contents.link'];
    }

    /**
     * アドオン投稿の追加項目.
     */
    public static function dataAddonPostValidation(): \Generator
    {
        yield 'ファイルIDが空' => [fn (): array => ['contents' => ['file' => '']], 'article.contents.file'];
        yield '存在しないファイルID' => [fn (): array => ['contents' => ['file' => 99999]], 'article.contents.file'];
        yield '他人の投稿したファイルID' => [fn (): array => ['contents' => ['file' => $this->user2_file->id]], 'article.contents.file'];
    }

    /**
     * マークダウンの追加項目.
     */
    public static function dataMarkdownValidation(): \Generator
    {
        yield 'markdownが無い' => [fn (): array => ['contents' => ['markdown' => null]], 'article.contents.markdown'];
        yield 'markdownが65536文字以上' => [fn (): array => ['contents' => ['markdown' => \str_repeat('a', 65536)]], 'article.contents.markdown'];
    }

    /**
     * 一般記事.
     */
    public static function dataPageValidation(): \Generator
    {
        yield 'セクションが無い' => [fn (): array => ['contents' => ['sections' => null]], 'article.contents.sections'];
        yield 'セクションが空' => [fn (): array => ['contents' => ['sections' => []]], 'article.contents.sections'];

        yield '本文セクションが空' => [fn (): array => ['contents' => ['sections' => [['type' => 'text', 'text' => '']]]], 'article.contents.sections.0.text'];
        yield '本文セクションが2049文字以上' => [fn (): array => ['contents' => ['sections' => [['type' => 'text', 'text' => str_repeat('a', 2049)]]]], 'article.contents.sections.0.text'];

        yield '見出しセクションが空' => [fn (): array => ['contents' => ['sections' => [['type' => 'caption', 'caption' => '']]]], 'article.contents.sections.0.caption'];
        yield '見出しセクションが256文字以上' => [fn (): array => ['contents' => ['sections' => [['type' => 'caption', 'caption' => str_repeat('a', 2049)]]]], 'article.contents.sections.0.caption'];

        yield 'URLセクションが空' => [fn (): array => ['contents' => ['sections' => [['type' => 'url', 'url' => '']]]], 'article.contents.sections.0.url'];
        yield 'URLセクションが不正な形式' => [fn (): array => ['contents' => ['sections' => [['type' => 'url', 'url' => 'not_url']]]], 'article.contents.sections.0.url'];

        yield '画像セクションが空' => [fn (): array => ['contents' => ['sections' => [['type' => 'image', 'id' => '']]]], 'article.contents.sections.0.id'];
        yield '画像セクションが存在しないID' => [fn (): array => ['contents' => ['sections' => [['type' => 'image', 'id' => 99999]]]], 'article.contents.sections.0.id'];
        yield '画像セクションが画像以外' => [fn (): array => ['contents' => ['sections' => [['type' => 'image', 'id' => $this->user_file->id]]]], 'article.contents.sections.0.id'];
        yield '画像セクションが他人の投稿したID' => [fn (): array => ['contents' => ['sections' => [['type' => 'image', 'id' => $this->user2_image->id]]]], 'article.contents.sections.0.id'];
    }
}
