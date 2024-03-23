<?php

declare(strict_types=1);

namespace Tests\Browser\Pages;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Laravel\Dusk\Browser;

class ListPakCategoryPage extends Page
{
    private readonly Article $article;

    private readonly Category $pak;

    private readonly Category $addon;

    public function __construct()
    {
        $user = User::factory()->create();
        $this->article = Article::factory()->publish()->addonPost()->create([
            'user_id' => $user->id,
        ]);
        $this->pak = Category::where('type', 'pak')->inRandomOrder()->firstOrFail();
        $this->addon = Category::where('type', 'addon')->inRandomOrder()->firstOrFail();
        $this->article->categories()->saveMany([$this->pak, $this->addon]);
    }

    public function url()
    {
        return sprintf('/categories/pak/%s/%s', $this->pak->slug, $this->addon->slug);
    }

    public function assert(Browser $browser): void
    {
        $browser
            ->waitForText(__(sprintf('category.%s.%s', $this->pak->type->value, $this->pak->slug)))
            ->waitForText(__(sprintf('category.%s.%s', $this->addon->type->value, $this->addon->slug)))
            ->assertSee($this->article->title);
    }
}
