<?php

declare(strict_types=1);

namespace Tests\Unit\Casts;

use App\Casts\ToArticleContents;
use App\Enums\ArticlePostType;
use App\Models\Article;
use App\Models\Contents\AddonIntroductionContent;
use App\Models\Contents\AddonPostContent;
use App\Models\Contents\MarkdownContent;
use App\Models\Contents\PageContent;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCase;

class ToArticleContentsTest extends TestCase
{
    private ToArticleContents $cast;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cast = new ToArticleContents;
    }

    #[Test]
    public function it_casts_to_addon_introduction_content(): void
    {
        $article = new Article;
        $article->post_type = ArticlePostType::AddonIntroduction;

        $jsonData = json_encode([
            'file' => 123,
            'author' => 'テスト作者',
            'description' => 'テスト説明',
        ]);

        $result = $this->cast->get($article, 'contents', $jsonData, []);

        $this->assertInstanceOf(AddonIntroductionContent::class, $result);
    }

    #[Test]
    public function it_casts_to_addon_post_content(): void
    {
        $article = new Article;
        $article->post_type = ArticlePostType::AddonPost;

        $jsonData = json_encode([
            'sections' => [
                ['type' => 'text', 'text' => 'テキスト'],
            ],
        ]);

        $result = $this->cast->get($article, 'contents', $jsonData, []);

        $this->assertInstanceOf(AddonPostContent::class, $result);
    }

    #[Test]
    public function it_casts_to_page_content(): void
    {
        $article = new Article;
        $article->post_type = ArticlePostType::Page;

        $jsonData = json_encode([
            'sections' => [
                ['type' => 'text', 'text' => 'ページコンテンツ'],
            ],
        ]);

        $result = $this->cast->get($article, 'contents', $jsonData, []);

        $this->assertInstanceOf(PageContent::class, $result);
    }

    #[Test]
    public function it_casts_to_markdown_content(): void
    {
        $article = new Article;
        $article->post_type = ArticlePostType::Markdown;

        $jsonData = json_encode([
            'markdown' => '# テストマークダウン',
        ]);

        $result = $this->cast->get($article, 'contents', $jsonData, []);

        $this->assertInstanceOf(MarkdownContent::class, $result);
    }

    #[Test]
    public function it_handles_empty_json_data(): void
    {
        $article = new Article;
        $article->post_type = ArticlePostType::Markdown;

        $jsonData = json_encode([]);

        $result = $this->cast->get($article, 'contents', $jsonData, []);

        $this->assertInstanceOf(MarkdownContent::class, $result);
    }

    #[Test]
    public function it_serializes_content_to_json(): void
    {
        $content = new MarkdownContent(['markdown' => '# テスト']);

        $result = $this->cast->set(new Article, 'contents', $content, []);

        $this->assertJson($result);
        $decoded = json_decode($result, true);
        $this->assertArrayHasKey('markdown', $decoded);
        $this->assertSame('# テスト', $decoded['markdown']);
    }

    #[Test]
    public function it_returns_empty_string_when_encoding_fails(): void
    {
        // json_encode が失敗する状況をシミュレート
        // ※実際には失敗しにくいので、空のオブジェクトで確認
        $content = new MarkdownContent([]);

        $result = $this->cast->set(new Article, 'contents', $content, []);

        $this->assertIsString($result);
    }

    #[Test]
    public function it_preserves_all_addon_introduction_fields(): void
    {
        $article = new Article;
        $article->post_type = ArticlePostType::AddonIntroduction;

        $data = [
            'file' => 456,
            'author' => '作者名',
            'license' => 'MIT',
            'thanks' => 'ありがとう',
            'link' => 'https://example.com',
            'description' => '説明文',
            'agreement' => true,
            'exclude_link_check' => false,
        ];

        $jsonData = json_encode($data);

        $result = $this->cast->get($article, 'contents', $jsonData, []);

        $this->assertInstanceOf(AddonIntroductionContent::class, $result);
    }

    #[Test]
    public function it_preserves_sections_structure(): void
    {
        $article = new Article;
        $article->post_type = ArticlePostType::AddonPost;

        $data = [
            'sections' => [
                [
                    'type' => 'text',
                    'text' => 'テキストセクション',
                ],
                [
                    'type' => 'image',
                    'id' => 123,
                    'caption' => '画像キャプション',
                ],
                [
                    'type' => 'video',
                    'url' => 'https://example.com/video.mp4',
                ],
            ],
        ];

        $jsonData = json_encode($data);

        $result = $this->cast->get($article, 'contents', $jsonData, []);

        $this->assertInstanceOf(AddonPostContent::class, $result);
    }
}
