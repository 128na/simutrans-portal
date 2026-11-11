<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages;

use App\Models\Article;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

/**
 * ヘッダーのキーワード検索欄（#660, #677）。
 * PC 幅では公開ページのどこでもヘッダーから検索でき、サイドメニューには検索欄を置かない。
 * スマホ幅はヘッダーの検索欄を隠し、モバイルメニュー内の検索を使う。
 */
class HeaderSearchTest extends TestCase
{
    /**
     * @return array<string, array{string, int}>
     */
    public static function pages(): array
    {
        return [
            'トップ' => ['index', 1],
            'pak64一覧' => ['pak.64', 1],
            '検索' => ['search', 1],
            // 記事詳細はデスクトップのサイドメニューを外している（#639）
            '記事詳細' => ['articles.show', 0],
        ];
    }

    #[DataProvider('pages')]
    public function test_公開ページはヘッダーに検索欄を出しサイドメニューには出さない(string $routeName, int $expectedAsides): void
    {
        $html = $this->pageHtml($routeName);
        $form = $this->headerSearchForm($html);

        $this->assertStringContainsString('method="GET"', $form);
        $this->assertStringContainsString('action="'.route('search').'"', $form);
        $this->assertStringContainsString('name="word"', $form);
        // スマホ幅ではヘッダーを2段にしないよう隠し、モバイルメニュー内の検索を使う
        $this->assertMatchesRegularExpression('/^<form\b[^>]*class="[^"]*\bhidden lg:block\b/', $form);
        $this->assertMatchesRegularExpression('/<form\b[^>]*role="search"[^>]*>(?:(?!<\/form>).)*id="sidebar-search-mobile"/s', $html);

        // デスクトップのサイドメニューには検索欄を置かない
        $asides = $this->asides($html);
        $this->assertCount($expectedAsides, $asides);
        foreach ($asides as $aside) {
            $this->assertStringNotContainsString('name="word"', $aside);
        }
    }

    public function test_検索ページは条件付きの検索フォームを残す(): void
    {
        $html = $this->get(route('search', ['word' => 'ヘッダー検索語']))->assertOk()->getContent();
        $this->assertIsString($html);

        // ヘッダーの欄は新しいキーワード検索用なので現在の語を入れない。条件を引き継ぐのはページ内のフォーム
        $this->assertStringNotContainsString('ヘッダー検索語', $this->headerSearchForm($html));
        $this->assertSame(1, preg_match('/<main\b.*?<\/main>/s', $html, $main));
        $this->assertStringContainsString('value="ヘッダー検索語"', $main[0]);
    }

    private function pageHtml(string $routeName): string
    {
        if ($routeName === 'articles.show') {
            $article = Article::factory()->publish()->page()->create();
            $url = route('articles.show', [
                'userIdOrNickname' => $article->user_id,
                'articleSlug' => $article->slug,
            ]);
        } else {
            $url = route($routeName);
        }

        $html = $this->get($url)->assertOk()->getContent();
        $this->assertIsString($html);

        return $html;
    }

    private function headerSearchForm(string $html): string
    {
        $this->assertSame(1, preg_match('/<header\b.*?<\/header>/s', $html, $header));
        $this->assertSame(
            1,
            preg_match('/<form\b[^>]*role="search"[^>]*>(?:(?!<\/form>).)*id="header-search"(?:(?!<\/form>).)*<\/form>/s', $header[0], $matches),
        );

        return $matches[0];
    }

    /**
     * @return list<string>
     */
    private function asides(string $html): array
    {
        preg_match_all('/<aside\b.*?<\/aside>/s', $html, $matches);

        return $matches[0];
    }
}
