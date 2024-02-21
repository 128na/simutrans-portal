<?php

declare(strict_types=1);

namespace Tests\Browser\Pages;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Laravel\Dusk\Browser;

class ArticleAddonPostPage extends Page
{
    private readonly Article $article;

    private readonly Category $category;

    private readonly Tag $tag;

    public function __construct()
    {
        $user = User::factory()->create();
        $this->article = Article::factory()->publish()->addonPost()->create(['user_id' => $user->id]);
        $this->category = Category::inRandomOrder()->first();
        $this->article->categories()->save($this->category);
        $this->tag = Tag::factory()->create();
        $this->article->tags()->save($this->tag);
    }

    public function url()
    {
        return sprintf('/users/%s/%s', $this->article->user_id, urlencode((string) $this->article->slug));
    }

    public function assert(Browser $browser): void
    {
        $browser
            ->waitForText($this->article->title)
            ->assertSee($this->article->title)
            ->assertSee(__(sprintf('category.%s.%s', $this->category->type, $this->category->slug)))
            ->assertSee($this->tag->name);
    }
}
