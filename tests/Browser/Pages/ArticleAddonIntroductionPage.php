<?php

namespace Tests\Browser\Pages;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Laravel\Dusk\Browser;

class ArticleAddonIntroductionPage extends Page
{
    private Article $article;

    public function __construct()
    {
        $user = User::factory()->create();
        $this->article = Article::factory()->publish()->addonIntroduction()->create(['user_id' => $user->id]);
        $this->category = Category::inRandomOrder()->first();
        $this->article->categories()->save($this->category);
        $this->tag = Tag::factory()->create();
        $this->article->tags()->save($this->tag);
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
        $browser->dump();
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
