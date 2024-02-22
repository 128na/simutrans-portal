<?php

declare(strict_types=1);

namespace Tests\Browser\Pages;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Laravel\Dusk\Browser;

class TagsPage extends Page
{
    private readonly Article $article;

    private readonly Tag $tag;

    public function __construct()
    {
        $user = User::factory()->create();
        $this->article = Article::factory()->publish()->addonPost()->create([
            'user_id' => $user->id,
        ]);
        $this->tag = Tag::factory()->create();
        $this->article->tags()->save($this->tag);
    }

    public function url()
    {
        return '/tags';
    }

    public function assert(Browser $browser): void
    {
        $browser
            ->waitForText($this->tag->name)
            ->assertSee($this->tag->name);
    }
}
