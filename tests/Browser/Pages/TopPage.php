<?php

declare(strict_types=1);

namespace Tests\Browser\Pages;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Laravel\Dusk\Browser;

final class TopPage extends Page
{
    private readonly Article $article;

    private readonly Category $category;

    public function __construct()
    {
        $user = User::factory()->create();
        $this->article = Article::factory()->publish()->addonPost()->create([
            'user_id' => $user->id,
        ]);
        $this->category = Category::where('type', 'pak')->where('slug', '128')->first();
        $this->article->categories()->save($this->category);
    }

    #[\Override]
    public function url()
    {
        return '/';
    }

    #[\Override]
    public function assert(Browser $browser): void
    {
        $browser
            ->waitForText($this->article->title)
            ->assertSee($this->article->title)
            ->assertSee('pak128の新着')
            ->assertSee(__(sprintf('category.%s.%s', $this->category->type->value, $this->category->slug)));
    }
}
