<?php

namespace Tests\Browser\Pages;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Laravel\Dusk\Browser;

class ListTagPage extends Page
{
    private Article $article;
    private Tag $tag;

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
        return "/tag/{$this->tag->id}";
    }

    public function assert(Browser $browser)
    {
        $browser
            ->waitForText($this->tag->name)
            ->assertSee($this->article->title)
        ;
    }
}