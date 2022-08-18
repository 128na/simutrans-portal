<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ArticleShowPage;
use Tests\Browser\Pages\TopPage;
use Tests\DuskTestCase;

class TopTest extends DuskTestCase
{
    /**
     * @dataProvider dataPages
     */
    public function testBasicExample(string $pageClass)
    {
        $this->browse(fn (Browser $browser) => $browser->visit(new $pageClass()));
    }

    public function dataPages()
    {
        yield 'トップページ' => [TopPage::class];
        yield '記事詳細' => [ArticleShowPage::class];
    }
}
