<?php

declare(strict_types=1);

namespace Tests\Browser\Pages;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Laravel\Dusk\Browser;

final class ListAnnouncePage extends Page
{
    private readonly Article $article;

    public function __construct()
    {
        $user = User::factory()->create();
        $this->article = Article::factory()->publish()->page()->create([
            'user_id' => $user->id,
        ]);
        $category = Category::where('type', 'page')->where('slug', 'announce')->firstOrFail();
        $this->article->categories()->save($category);
    }

    public function url()
    {
        return '/announces';
    }

    public function assert(Browser $browser): void
    {
        $browser
            ->waitForText($this->article->title)
            ->assertSee($this->article->title);
    }
}
