<?php

namespace Tests\Browser\Pages;

use App\Models\Article;
use Laravel\Dusk\Browser;

class ArticleShowPage extends Page
{
    private Article $article;

    public function __construct()
    {
        $this->article = Article::factory()->create();
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/articles/'.urlencode($this->article->slug);
    }

    /**
     * Assert that the browser is on the page.
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser
            ->assertSee($this->article->title);
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@element' => '#selector',
        ];
    }
}
