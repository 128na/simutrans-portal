<?php

namespace Tests\Browser\Pages;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Laravel\Dusk\Browser;

class ListPakCategoryPage extends Page
{
    private Article $article;
    private Category $pak;
    private Category $addon;

    public function __construct()
    {
        $user = User::factory()->create();
        $this->article = Article::factory()->publish()->addonPost()->create([
            'user_id' => $user->id,
        ]);
        $this->pak = Category::where('type', 'pak')->inRandomOrder()->first();
        $this->addon = Category::where('type', 'addon')->inRandomOrder()->first();
        $this->article->categories()->saveMany([$this->pak, $this->addon]);
    }

    public function url()
    {
        return "/category/pak/{$this->pak->slug}/{$this->addon->slug}";
    }

    public function assert(Browser $browser)
    {
        $browser
            ->waitForText(__("category.{$this->pak->type}.{$this->pak->slug}"))
            ->waitForText(__("category.{$this->addon->type}.{$this->addon->slug}"))
            ->assertSee($this->article->title)
        ;
    }
}
