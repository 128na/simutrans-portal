<?php

declare(strict_types=1);

namespace Tests\Browser;

use PHPUnit\Framework\Attributes\DataProvider;
use App\Jobs\Article\JobUpdateRelated;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ArticleAddonIntroductionPage;
use Tests\Browser\Pages\ArticleAddonPostPage;
use Tests\Browser\Pages\ArticleMarkdownPage;
use Tests\Browser\Pages\ArticlePagePage;
use Tests\Browser\Pages\ListAnnouncePage;
use Tests\Browser\Pages\ListCategoryPage;
use Tests\Browser\Pages\ListPagePage;
use Tests\Browser\Pages\ListPakCategoryPage;
use Tests\Browser\Pages\ListSearchPage;
use Tests\Browser\Pages\ListTagPage;
use Tests\Browser\Pages\ListUserPage;
use Tests\Browser\Pages\MypagePage;
use Tests\Browser\Pages\TagsPage;
use Tests\Browser\Pages\TopPage;
use Tests\DuskTestCase;

class PageTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[DataProvider('dataPages')]
    public function testPages(string $pageClass): void
    {
        $page = new $pageClass();
        JobUpdateRelated::dispatchSync();
        $this->browse(fn (Browser $browser) => $browser
            ->visit($page)
        );
    }

    public static function dataPages(): array
    {
        yield '記事詳細_アドオン投稿' => [ArticleAddonPostPage::class];
        yield '記事詳細_アドオン紹介' => [ArticleAddonIntroductionPage::class];
        yield '記事詳細_markdown' => [ArticleMarkdownPage::class];
        yield '記事詳細_ページ' => [ArticlePagePage::class];
        yield 'マイページ' => [MypagePage::class];
        yield '一覧ページ（タグ）' => [ListTagPage::class];
        yield '一覧ページ（カテゴリ）' => [ListCategoryPage::class];
        yield '一覧ページ（pak&カテゴリ）' => [ListPakCategoryPage::class];
        yield '一覧ページ（ユーザー）' => [ListUserPage::class];
        yield '一覧ページ（お知らせ）' => [ListAnnouncePage::class];
        yield '一覧ページ（一般記事）' => [ListPagePage::class];
        yield '一覧ページ（検索）' => [ListSearchPage::class];
        yield 'タグ一覧' => [TagsPage::class];

        // キャッシュの影響なのか最後にする必要あり
        yield 'トップページ' => [TopPage::class];
    }
}
