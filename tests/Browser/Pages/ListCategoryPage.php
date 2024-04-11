<?php

declare(strict_types=1);

namespace Tests\Browser\Pages;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Laravel\Dusk\Browser;

final class ListCategoryPage extends Page
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

    #[\Override]
    public function url()
    {
        return sprintf('/categories/%s/%s', $this->category->type->value, $this->category->slug);
    }

    #[\Override]
    public function assert(Browser $browser): void
    {
        $browser
            ->waitForText(__(sprintf('category.%s.%s', $this->category->type->value, $this->category->slug)))
            ->assertSee($this->article->title);
    }
}
