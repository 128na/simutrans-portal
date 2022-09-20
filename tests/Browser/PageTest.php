<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ArticleAddonIntroductionPage;
use Tests\Browser\Pages\ArticleAddonPostPage;
use Tests\Browser\Pages\ArticleMarkdownPage;
use Tests\Browser\Pages\ArticlePagePage;
use Tests\Browser\Pages\MypagePage;
use Tests\Browser\Pages\TopPage;
use Tests\DuskTestCase;

class PageTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @dataProvider dataPages
     */
    public function testPages(string $pageClass)
    {
        $this->browse(fn (Browser $browser) => $browser->visit(new $pageClass()));
    }

    public function dataPages()
    {
        yield 'トップページ' => [TopPage::class];
        yield '記事詳細_アドオン投稿' => [ArticleAddonPostPage::class];
        yield '記事詳細_アドオン紹介' => [ArticleAddonIntroductionPage::class];
        yield '記事詳細_markdown' => [ArticleMarkdownPage::class];
        yield '記事詳細_ページ' => [ArticlePagePage::class];
        yield 'マイページ' => [MypagePage::class];
    }
}
