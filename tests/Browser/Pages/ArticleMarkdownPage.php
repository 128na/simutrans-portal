<?php

namespace Tests\Browser\Pages;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Laravel\Dusk\Browser;

class ArticleMarkdownPage extends Page
{
    private Article $article;

    private Category $category;

    public function __construct()
    {
        $user = User::factory()->create();
        $this->article = Article::factory()->publish()->markdown()->create([
            'user_id' => $user->id,
            'contents' => ['markdown' => '# Hoge'],
        ]);
        $this->category = Category::inRandomOrder()->first();
        $this->article->categories()->save($this->category);
    }

    public function url()
    {
        return '/articles/'.urlencode($this->article->slug);
    }

    public function assert(Browser $browser)
    {
        $browser
            ->waitForText($this->article->title)
            ->assertSee($this->article->title)
            ->assertSee('Hoge')
            ->assertSee(__("category.{$this->category->type}.{$this->category->slug}"));
    }
}
