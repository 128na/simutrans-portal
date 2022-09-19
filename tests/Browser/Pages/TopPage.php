<?php

namespace Tests\Browser\Pages;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Laravel\Dusk\Browser;

class TopPage extends Page
{
    private Article $article;
    private Category $category;

    public function __construct()
    {
        $user = User::factory()->create();
        $this->article = Article::factory()->publish()->addonPost()->create([
            'title' => 'dummy title',
            'user_id' => $user->id,
        ]);
        $this->category = Category::where('type', 'pak')->where('slug', '128')->first();
        $this->article->categories()->save($this->category);
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser
            ->waitForText($this->article->title, 30)
            ->assertSee($this->article->title)
            ->assertSee('の新着アドオン')
            ->assertSee(__("category.{$this->category->type}.{$this->category->slug}"))
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
