<?php

declare(strict_types=1);

namespace Tests\Browser\Pages;

use App\Models\Article;
use App\Models\User;
use Laravel\Dusk\Browser;

final class ListSearchPage extends Page
{
    private readonly Article $article;

    public function __construct()
    {
        $user = User::factory()->create();
        $this->article = Article::factory()
            ->publish()
            ->addonPost()
            ->create([
                'title' => 'dummy title',
                'user_id' => $user->id,
            ]);
    }

    #[\Override]
    public function url()
    {
        return '/search?word=dummy';
    }

    #[\Override]
    public function assert(Browser $browser): void
    {
        $browser->waitForText($this->article->title)->assertSee($this->article->title);
    }
}
