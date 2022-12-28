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
            'user_id' => $user->id,
        ]);
        $this->category = Category::where('type', 'pak')->where('slug', '128')->first();
        $this->article->categories()->save($this->category);
    }

    public function url()
    {
        return '/';
    }

    public function assert(Browser $browser)
    {
        $browser
            ->waitForText($this->article->title)
            ->assertSee($this->article->title)
            ->assertSee('pak128の新着')
            ->assertSee(__("category.{$this->category->type}.{$this->category->slug}"));
    }
}
