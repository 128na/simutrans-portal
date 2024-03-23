<?php

declare(strict_types=1);

namespace Tests\Browser\Pages;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Laravel\Dusk\Browser;

class ArticleMarkdownPage extends Page
{
    private readonly Article $article;

    private readonly Category $category;

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
        return sprintf('/users/%s/%s', $this->article->user_id, urlencode((string) $this->article->slug));
    }

    public function assert(Browser $browser): void
    {
        $browser
            ->waitForText($this->article->title)
            ->assertSee($this->article->title)
            ->assertSee('Hoge')
            ->assertSee(__(sprintf('category.%s.%s', $this->category->type->value, $this->category->slug)));
    }
}
