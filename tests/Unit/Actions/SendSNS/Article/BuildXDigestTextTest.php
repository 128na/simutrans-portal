<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\SendSNS\Article;

use App\Actions\SendSNS\Article\BuildXDigestText;
use App\Actions\SendSNS\Article\Data\XDigestArticle;
use App\Actions\SendSNS\Article\Data\XDigestArticles;
use App\Enums\XDigestArticleType;
use App\Models\Article;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Tests\Unit\TestCase;
use Twitter\Text\Parser;

class BuildXDigestTextTest extends TestCase
{
    public function test_title_within_the_cap_is_kept_as_is(): void
    {
        // ASCII(重み1)54文字ちょうど: 省略されないはず
        $title = str_repeat('a', 54);
        $digest = $this->digestOf([$this->digestArticle(1, $title)]);

        $text = app(BuildXDigestText::class)($digest);

        $this->assertStringContainsString($title, $text);
        $this->assertStringNotContainsString('…', $text);
    }

    public function test_long_title_is_truncated_to_the_weighted_cap_with_ellipsis(): void
    {
        // ASCII(重み1)60文字: 重み付き54文字を超えるため省略される
        $title = str_repeat('a', 60);
        $digest = $this->digestOf([$this->digestArticle(1, $title)]);

        $text = app(BuildXDigestText::class)($digest);

        // 元タイトルそのままは含まれない(=省略された)
        $this->assertStringNotContainsString($title, $text);

        $truncatedTitle = $this->extractTitleLine($text, $title);

        $this->assertStringEndsWith('…', $truncatedTitle);
        $this->assertTrue(str_starts_with($title, mb_substr($truncatedTitle, 0, -1)));
        $this->assertLessThanOrEqual(54, $this->parser()->parseTweet($truncatedTitle)->weightedLength);
    }

    public function test_falls_back_to_shorter_titles_when_total_text_exceeds_280_weighted_chars(): void
    {
        // 3件とも重み付き54文字ギリギリまで長い日本語タイトルにすると、
        // デフォルト上限(54)のままでは本文全体が280文字を超える。
        $items = [
            $this->digestArticle(1, str_repeat('あ', 60), 'slug-one'),
            $this->digestArticle(2, str_repeat('い', 60), 'slug-two'),
            $this->digestArticle(3, str_repeat('う', 60), 'slug-three'),
        ];
        $digest = $this->digestOf($items, totalCount: 3);

        $text = app(BuildXDigestText::class)($digest);

        $this->assertLessThanOrEqual(280, $this->parser()->parseTweet($text)->weightedLength);

        // 掲載件数を減らさず、3件とも(省略された形であっても)本文に含まれ続ける
        $this->assertSame(3, substr_count($text, '/users/'));
        $this->assertStringContainsString('slug-one', $text);
        $this->assertStringContainsString('slug-two', $text);
        $this->assertStringContainsString('slug-three', $text);
    }

    public function test_more_count_line_is_appended_only_when_total_exceeds_displayed_items(): void
    {
        $items = [
            $this->digestArticle(1, 'title one'),
            $this->digestArticle(2, 'title two'),
            $this->digestArticle(3, 'title three'),
        ];

        $textWithMore = app(BuildXDigestText::class)($this->digestOf($items, totalCount: 5));
        $textWithoutMore = app(BuildXDigestText::class)($this->digestOf($items, totalCount: 3));

        $this->assertStringContainsString('ほか2件', $textWithMore);
        $this->assertStringNotContainsString('ほか', $textWithoutMore);
    }

    private function digestArticle(int $id, string $title, string $slug = 'article-slug'): XDigestArticle
    {
        // user_idを明示指定し、Article::factory()のデフォルト(User::factory())による
        // 暗黙のDB作成(Laravelのネストしたfactory解決は make() でもcreate()される)を避ける。
        $user = User::factory()->make(['id' => $id, 'nickname' => 'user'.$id]);
        $article = Article::factory()->make([
            'id' => $id,
            'user_id' => $id,
            'title' => $title,
            'slug' => $slug.'-'.$id,
        ]);
        $article->setRelation('user', $user);

        return new XDigestArticle($article, XDigestArticleType::Publish, CarbonImmutable::now());
    }

    /**
     * @param  list<XDigestArticle>  $items
     */
    private function digestOf(array $items, ?int $totalCount = null): XDigestArticles
    {
        $collection = Collection::make($items);

        return new XDigestArticles($collection, $totalCount ?? $collection->count());
    }

    private function extractTitleLine(string $text, string $originalTitle): string
    {
        foreach (explode("\n", $text) as $line) {
            if ($line !== '' && str_starts_with($originalTitle, mb_substr($line, 0, -1))) {
                return $line;
            }
        }

        $this->fail('truncated title line not found in text: '.$text);
    }

    private function parser(): Parser
    {
        return app(Parser::class);
    }
}
