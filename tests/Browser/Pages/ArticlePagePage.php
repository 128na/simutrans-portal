<?php

namespace Tests\Browser\Pages;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\User;
use Laravel\Dusk\Browser;

class ArticlePagePage extends Page
{
    private Article $article;

    public function __construct()
    {
        $user = User::factory()->create();
        $this->article = Article::factory()->publish()->page()->create([
            'user_id' => $user->id,
        ]);
        $this->attachment = Attachment::factory()->image()->create([
            'user_id' => $user->id,
            'attachmentable_type' => Article::class,
            'attachmentable_id' => $this->article->id,
        ]);
        $this->article->update(['contents' => ['sections' => [
            ['type' => 'caption', 'caption' => 'DummyCaption'],
            ['type' => 'text', 'text' => 'DummyText'],
            ['type' => 'url', 'url' => 'http://example.com'],
            ['type' => 'image', 'id' => $this->attachment->id],
        ]]]);

        $this->category = Category::inRandomOrder()->first();
        $this->article->categories()->save($this->category);
    }

    public function url()
    {
        return '/articles/'.urlencode($this->article->slug);
    }

    public function assert(Browser $browser)
    {
        $browser
            ->waitForText($this->article->title)
            ->assertSee($this->article->title)
            ->assertSee('DummyCaption')
            ->assertSee('DummyText')
            ->assertSee('http://example.com');
    }
}
