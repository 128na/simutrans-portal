<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages;

use App\Models\User;
use Tests\Feature\TestCase;

/**
 * サイドメニューの現在地表示（#637）。
 * 現在地は aria-current="page" を付けて背景色で示し、他の項目には付けない。
 */
class SidebarNavigationTest extends TestCase
{
    public function test_公開側は表示中のページの項目だけを現在地にする(): void
    {
        $testResponse = $this->get(route('pak.64'));

        $testResponse->assertOk();
        $html = $testResponse->getContent();
        $this->assertIsString($html);

        // デスクトップのサイドバーとモバイルメニューの2か所
        $this->assertSame(2, $this->countCurrent($html, route('pak.64')));
        $this->assertSame(0, $this->countCurrent($html, route('pak.128')));
        $this->assertSame(0, $this->countCurrent($html, route('latest')));
        $this->assertSame(2, substr_count($html, 'aria-current="page"'));
    }

    public function test_マイページは前方一致の親項目を現在地にしない(): void
    {
        $this->actingAs(User::factory()->create());

        $testResponse = $this->get(route('mypage.articles.index'));

        $testResponse->assertOk();
        $html = $testResponse->getContent();
        $this->assertIsString($html);

        $this->assertSame(2, $this->countCurrent($html, route('mypage.articles.index')));
        $this->assertSame(0, $this->countCurrent($html, route('mypage.index')));
        $this->assertSame(2, substr_count($html, 'aria-current="page"'));
    }

    private function countCurrent(string $html, string $url): int
    {
        return preg_match_all(
            '/<a\s+href="'.preg_quote(e($url), '/').'"\s+aria-current="page"/',
            $html
        ) ?: 0;
    }
}
