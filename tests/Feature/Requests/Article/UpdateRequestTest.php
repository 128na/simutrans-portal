<?php

declare(strict_types=1);

namespace Tests\Feature\Requests\Article;

use App\Enums\ArticlePostType;
use App\Http\Requests\Api\Article\UpdateRequest;
use App\Models\Article;
use App\Models\User;
use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

final class UpdateRequestTest extends TestCase
{
    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[DataProvider('dataFail')]
    public function testFail(ArticlePostType $articlePostType, Closure $setup, string $expectedErrorField): void
    {
        $article = match ($articlePostType) {
            ArticlePostType::AddonIntroduction => $this->createAddonIntroduction($this->user),
            ArticlePostType::AddonPost => $this->createAddonPost($this->user),
            ArticlePostType::Page => $this->createPage($this->user),
            ArticlePostType::Markdown => $this->createMarkdown($this->user),
        };

        $data = ['article' => ['id' => $article->id, 'post_type' => $articlePostType->value, ...$setup($this)]];

        $this->actingAs($this->user);
        $messageBag = $this->makeValidator(UpdateRequest::class, $data)->errors();
        $this->assertArrayHasKey($expectedErrorField, $messageBag->toArray());
    }

    public static function dataFail(): \Generator
    {
        yield 'AddonIntroduction ステータスが空' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['status' => ''], 'article.status',
        ];
        yield 'AddonIntroduction 不正なステータス' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['status' => 'test_example'], 'article.status',
        ];
        yield 'AddonIntroduction タイトルが空' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['title' => ''], 'article.title',
        ];
        yield 'AddonIntroduction タイトルが256文字以上' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['title' => str_repeat('a', 256)], 'article.title',
        ];
        yield 'AddonIntroduction タイトルが重複' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['title' => Article::factory()->create()->title], 'article.title',
        ];
        yield 'AddonIntroduction タイトルにNG文字' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['title' => '@example'], 'article.title',
        ];
        yield 'AddonIntroduction スラッグが空' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['slug' => ''], 'article.slug',
        ];
        yield 'AddonIntroduction スラッグが256文字以上' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['slug' => str_repeat('a', 256)], 'article.slug',
        ];
        yield 'AddonIntroduction 存在しないサムネイルID' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['contents' => ['thumbnail' => 99999]], 'article.contents.thumbnail',
        ];
        yield 'AddonIntroduction 画像以外' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['contents' => ['thumbnail' => $self->createAttachment($self->user)->id]], 'article.contents.thumbnail',
        ];
        yield 'AddonIntroduction 他人の投稿したサムネイルID' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['contents' => ['thumbnail' => $self->createImageAttachment(User::factory()->create())->id]], 'article.contents.thumbnail',
        ];
        yield 'AddonIntroduction カテゴリがnull' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['categories' => null], 'article.categories',
        ];
        yield 'AddonIntroduction 存在しないカテゴリ' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['categories' => [['id' => 99999]]], 'article.categories.0.id',
        ];
        yield 'AddonIntroduction タグ名がnull' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['tags' => null], 'article.tags',
        ];
        yield 'AddonIntroduction 存在しないタグ' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['tags' => [['id' => -1]]], 'article.tags.0.id',
        ];
        yield 'AddonIntroduction 説明が空' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['contents' => ['description' => '']], 'article.contents.description',
        ];
        yield 'AddonIntroduction 説明が2049文字以上' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['contents' => ['description' => str_repeat('a', 2049)]], 'article.contents.description',
        ];
        yield 'AddonIntroduction 謝辞が2049文字以上' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['contents' => ['thanks' => str_repeat('a', 2049)]], 'article.contents.thanks',
        ];
        yield 'AddonIntroduction ライセンス（その他）が2049文字以上' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['contents' => ['license' => str_repeat('a', 2049)]], 'article.contents.license',
        ];
        // アドオン紹介の追加項目
        yield 'AddonIntroduction アドオン作者が空' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['contents' => ['author' => '']], 'article.contents.author',
        ];
        yield 'AddonIntroduction アドオン作者が256文字以上' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['contents' => ['author' => str_repeat('a', 256)]], 'article.contents.author',
        ];
        yield 'AddonIntroduction リンクが空' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['contents' => ['link' => '']], 'article.contents.link',
        ];
        yield 'AddonIntroduction リンクが不正なURL' => [
            ArticlePostType::AddonIntroduction,
            fn (self $self): array => ['contents' => ['link' => 'not_url']], 'article.contents.link',
        ];
        // アドオン投稿の追加項目
        yield 'AddonPost ファイルIDが空' => [
            ArticlePostType::AddonPost,
            fn (self $self): array => ['contents' => ['file' => '']], 'article.contents.file',
        ];
        yield 'AddonPost 存在しないファイルID' => [
            ArticlePostType::AddonPost,
            fn (self $self): array => ['contents' => ['file' => 99999]], 'article.contents.file',
        ];
        yield 'AddonPost 他人の投稿したファイルID' => [
            ArticlePostType::AddonPost,
            fn (self $self): array => ['contents' => ['file' => $self->createAttachment(User::factory()->create())->id]], 'article.contents.file',
        ];
        // マークダウンの追加項目
        yield 'Markdown markdownが無い' => [
            ArticlePostType::Markdown,
            fn (self $self): array => ['contents' => ['markdown' => null]], 'article.contents.markdown',
        ];
        yield 'Markdown markdownが65536文字以上' => [
            ArticlePostType::Markdown,
            fn (self $self): array => ['contents' => ['markdown' => str_repeat('a', 65536)]], 'article.contents.markdown',
        ];
        // 一般記事
        yield 'Page セクションが無い' => [
            ArticlePostType::Page,
            fn (self $self): array => ['contents' => ['sections' => null]], 'article.contents.sections',
        ];
        yield 'Page セクションが空' => [
            ArticlePostType::Page,
            fn (self $self): array => ['contents' => ['sections' => []]], 'article.contents.sections',
        ];
        yield 'Page 本文セクションが空' => [
            ArticlePostType::Page,
            fn (self $self): array => ['contents' => ['sections' => [['type' => 'text', 'text' => '']]]], 'article.contents.sections.0.text',
        ];
        yield 'Page 本文セクションが2049文字以上' => [
            ArticlePostType::Page,
            fn (self $self): array => ['contents' => ['sections' => [['type' => 'text', 'text' => str_repeat('a', 2049)]]]], 'article.contents.sections.0.text',
        ];
        yield 'Page 見出しセクションが空' => [
            ArticlePostType::Page,
            fn (self $self): array => ['contents' => ['sections' => [['type' => 'caption', 'caption' => '']]]], 'article.contents.sections.0.caption',
        ];
        yield 'Page 見出しセクションが256文字以上' => [
            ArticlePostType::Page,
            fn (self $self): array => ['contents' => ['sections' => [['type' => 'caption', 'caption' => str_repeat('a', 2049)]]]], 'article.contents.sections.0.caption',
        ];
        yield 'Page URLセクションが空' => [
            ArticlePostType::Page,
            fn (self $self): array => ['contents' => ['sections' => [['type' => 'url', 'url' => '']]]], 'article.contents.sections.0.url',
        ];
        yield 'Page URLセクションが不正な形式' => [
            ArticlePostType::Page,
            fn (self $self): array => ['contents' => ['sections' => [['type' => 'url', 'url' => 'not_url']]]], 'article.contents.sections.0.url',
        ];
        yield 'Page 画像セクションが空' => [
            ArticlePostType::Page,
            fn (self $self): array => ['contents' => ['sections' => [['type' => 'image', 'id' => '']]]], 'article.contents.sections.0.id',
        ];
        yield 'Page 画像セクションが存在しないID' => [
            ArticlePostType::Page,
            fn (self $self): array => ['contents' => ['sections' => [['type' => 'image', 'id' => 99999]]]], 'article.contents.sections.0.id',
        ];
        yield 'Page 画像セクションが画像以外' => [
            ArticlePostType::Page,
            fn (self $self): array => ['contents' => ['sections' => [['type' => 'image', 'id' => $self->createAttachment($self->user)->id]]]], 'article.contents.sections.0.id',
        ];
        yield 'Page 画像セクションが他人の投稿したID' => [
            ArticlePostType::Page,
            fn (self $self): array => ['contents' => ['sections' => [['type' => 'image', 'id' => $self->createAttachment(User::factory()->create())->id]]]], 'article.contents.sections.0.id',
        ];
    }
}
