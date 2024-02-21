<?php

declare(strict_types=1);

namespace Tests\Browser\Pages;

use App\Models\Article;
use App\Models\User;
use Laravel\Dusk\Browser;

class ListUserPage extends Page
{
    private readonly Article $article;

    private readonly User $user;

    public function __construct()
    {
        $this->user = User::factory()->create();
        $this->article = Article::factory()->publish()->addonPost()->create([
            'user_id' => $this->user->id,
        ]);
    }

    public function url()
    {
        return '/users/' . $this->user->id;
    }

    public function assert(Browser $browser): void
    {
        $browser
            ->waitForText($this->user->name)
            ->assertSee($this->article->title);
    }
}
