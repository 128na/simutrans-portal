<?php

declare(strict_types=1);

namespace Tests\Browser\Pages;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Laravel\Dusk\Browser;

class ArticleAddonIntroductionPage extends Page
{
    private Article $article;

    private Category $category;

    private Tag $tag;

    public function __construct()
    {
        $user = User::factory()->create();
        $this->article = Article::factory()->publish()->addonIntroduction()->create(['user_id' => $user->id]);
        $this->category = Category::inRandomOrder()->first();
        $this->article->categories()->save($this->category);
        $this->tag = Tag::factory()->create();
        $this->article->tags()->save($this->tag);
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
            ->assertSee(__("category.{$this->category->type}.{$this->category->slug}"))
            ->assertSee($this->tag->name);
    }
}
