<?php

namespace Tests\Browser\Pages;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Laravel\Dusk\Browser;

class ArticleAddonPostPage extends Page
{
    private Article $article;
    private Category $category;
    private Tag $tag;

    public function __construct()
    {
        $user = User::factory()->create();
        $this->article = Article::factory()->publish()->addonPost()->create(['user_id' => $user->id]);
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
        $browser
            ->waitForText($this->article->title)
            ->assertSee($this->article->title)
            ->assertSee(__("category.{$this->category->type}.{$this->category->slug}"))
            ->assertSee($this->tag->name)
        ;
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
