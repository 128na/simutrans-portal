<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\ArticlePostType;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCase;

class ArticlePostTypeTest extends TestCase
{
    #[Test]
    public function it_has_correct_addon_post_value(): void
    {
        $this->assertSame('addon-post', ArticlePostType::AddonPost->value);
    }

    #[Test]
    public function it_has_correct_addon_introduction_value(): void
    {
        $this->assertSame('addon-introduction', ArticlePostType::AddonIntroduction->value);
    }

    #[Test]
    public function it_has_correct_page_value(): void
    {
        $this->assertSame('page', ArticlePostType::Page->value);
    }

    #[Test]
    public function it_has_correct_markdown_value(): void
    {
        $this->assertSame('markdown', ArticlePostType::Markdown->value);
    }

    #[Test]
    public function it_has_all_expected_cases(): void
    {
        $cases = ArticlePostType::cases();

        $this->assertCount(4, $cases);
        $this->assertContains(ArticlePostType::AddonPost, $cases);
        $this->assertContains(ArticlePostType::AddonIntroduction, $cases);
        $this->assertContains(ArticlePostType::Page, $cases);
        $this->assertContains(ArticlePostType::Markdown, $cases);
    }

    #[Test]
    public function it_can_be_constructed_from_value(): void
    {
        $addonPost = ArticlePostType::from('addon-post');
        $this->assertSame(ArticlePostType::AddonPost, $addonPost);

        $addonIntroduction = ArticlePostType::from('addon-introduction');
        $this->assertSame(ArticlePostType::AddonIntroduction, $addonIntroduction);

        $page = ArticlePostType::from('page');
        $this->assertSame(ArticlePostType::Page, $page);

        $markdown = ArticlePostType::from('markdown');
        $this->assertSame(ArticlePostType::Markdown, $markdown);
    }

    #[Test]
    public function it_throws_exception_for_invalid_value(): void
    {
        $this->expectException(\ValueError::class);

        ArticlePostType::from('invalid-type');
    }

    #[Test]
    public function it_returns_null_for_invalid_value_with_try_from(): void
    {
        $result = ArticlePostType::tryFrom('invalid-type');

        $this->assertNull($result);
    }

    #[Test]
    public function it_can_be_compared(): void
    {
        $type1 = ArticlePostType::AddonPost;
        $type2 = ArticlePostType::AddonPost;
        $type3 = ArticlePostType::Page;

        $this->assertTrue($type1 === $type2);
        $this->assertFalse($type1 === $type3);
    }
}
