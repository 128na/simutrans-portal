<?php

declare(strict_types=1);

namespace Tests\Browser\Pages;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Laravel\Dusk\Browser;

class ListCategoryPage extends Page
{
    private readonly Article $article;

    private readonly Category $category;

    public function __construct()
    {
        $user = User::factory()->create();
        $this->article = Article::factory()->publish()->addonPost()->create([
            'user_id' => $user->id,
        ]);
        $this->category = Category::inRandomOrder()->first();
        $this->article->categories()->save($this->category);
    }

    public function url()
    {
        return "/categories/{$this->category->type}/{$this->category->slug}";
    }

    public function assert(Browser $browser)
    {
        $browser
            ->waitForText(__("category.{$this->category->type}.{$this->category->slug}"))
            ->assertSee($this->article->title);
    }
}
