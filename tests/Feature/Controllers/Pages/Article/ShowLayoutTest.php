<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages\Article;

use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Category;
use Tests\Feature\TestCase;

/**
 * 記事詳細のレイアウト（#639）。
 * 本文幅を広げるためデスクトップのサイドメニューを外し、代わりに現在位置（トップ / pak / 種類）を出す。
 */
class ShowLayoutTest extends TestCase
{
    public function test_アドオン記事は現在位置にpakと種類を出しサイドメニューを外す(): void
    {
        $article = Article::factory()->publish()->addonIntroduction()->create();
        $article->categories()->sync([
            $this->category(CategoryType::Pak, '128-japan')->id,
            $this->category(CategoryType::Addon, 'trains')->id,
        ]);

        $html = $this->showHtml($article);

        $this->assertSame([
            route('index'),
            route('pak.128japan'),
            route('categories.pakAddon', ['pak' => '128-japan', 'addon' => 'trains']),
        ], $this->breadcrumbUrls($html));
        $this->assertStringContainsString('Pak128.Japan', $this->breadcrumb($html));
        $this->assertStringContainsString('鉄道車両', $this->breadcrumb($html));

        // モバイルメニューの1か所だけ残る（他のページはデスクトップのサイドメニューと合わせて2か所）
        $this->assertSame(1, substr_count($html, 'aria-label="サイトメニュー"'));
    }

    public function test_pak問わずより具体的なpakを優先し主要3種以外はその他のpakへつなぐ(): void
    {
        $article = Article::factory()->publish()->addonPost()->create();
        $article->categories()->sync([
            $this->category(CategoryType::Pak, 'any')->id,
            $this->category(CategoryType::Pak, '256')->id,
            $this->category(CategoryType::Addon, 'buildings')->id,
            $this->category(CategoryType::Addon, 'trains')->id,
        ]);

        $this->assertSame([
            route('index'),
            route('pak.others'),
            route('categories.pakAddon', ['pak' => '256', 'addon' => 'trains']),
        ], $this->breadcrumbUrls($this->showHtml($article)));
    }

    public function test_pak未指定のアドオン記事は種類を検索へつなぐ(): void
    {
        $article = Article::factory()->publish()->addonIntroduction()->create();
        $addon = $this->category(CategoryType::Addon, 'ships');
        $article->categories()->sync([$addon->id]);

        $this->assertSame([
            route('index'),
            route('search', ['categoryIds' => [$addon->id]]),
        ], $this->breadcrumbUrls($this->showHtml($article)));
    }

    public function test_一般記事はトップだけを出す(): void
    {
        $article = Article::factory()->publish()->page()->create();
        $article->categories()->sync([$this->category(CategoryType::Pak, '64')->id]);

        $this->assertSame([route('index')], $this->breadcrumbUrls($this->showHtml($article)));
    }

    public function test_他のページはサイドメニューを残す(): void
    {
        $html = $this->get(route('pak.64'))->assertOk()->getContent();
        $this->assertIsString($html);

        $this->assertSame(2, substr_count($html, 'aria-label="サイトメニュー"'));
        $this->assertStringNotContainsString('aria-label="現在位置"', $html);
    }

    private function category(CategoryType $categoryType, string $slug): Category
    {
        return Category::query()->where('type', $categoryType)->where('slug', $slug)->firstOrFail();
    }

    private function showHtml(Article $article): string
    {
        $html = $this->get(route('articles.show', [
            'userIdOrNickname' => $article->user_id,
            'articleSlug' => $article->slug,
        ]))->assertOk()->getContent();
        $this->assertIsString($html);

        return $html;
    }

    private function breadcrumb(string $html): string
    {
        $this->assertSame(1, preg_match('/<nav aria-label="現在位置".*?<\/nav>/s', $html, $matches));

        return $matches[0] ?? '';
    }

    /**
     * @return list<string>
     */
    private function breadcrumbUrls(string $html): array
    {
        preg_match_all('/<a href="([^"]*)"/', $this->breadcrumb($html), $matches);

        return array_map(html_entity_decode(...), $matches[1]);
    }
}
