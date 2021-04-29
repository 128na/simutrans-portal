<?php

namespace Tests\Feature\Controllers\Front\ArticleController;

use App\Models\Article;
use App\Models\Category;
use Tests\TestCase;

class CategoryPakAddonTest extends TestCase
{
    private Article $article1;
    private Article $article2;
    private Category $pak;
    private Category $addon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article1 = Article::factory()->publish()->addonIntroduction()->create();
        $this->article2 = Article::factory()->publish()->addonIntroduction()->create();
        $this->article3 = Article::factory()->publish()->addonIntroduction()->create();
        $this->article4 = Article::factory()->publish()->addonIntroduction()->create();

        $this->pak = Category::pak()->inRandomOrder()->first();
        $this->addon = Category::addon()->inRandomOrder()->first();
        $this->article1->categories()->saveMany([$this->pak, $this->addon]);

        $this->article2->categories()->saveMany([$this->pak]);
        $this->article3->categories()->saveMany([$this->addon]);
    }

    public function test()
    {
        $url = route('category.pak.addon', [$this->pak->slug, $this->addon->slug]);
        $res = $this->get($url);
        $res->assertOk();
        $res->assertSeeText($this->article1->title);
        $res->assertDontSeeText($this->article2->title);
        $res->assertDontSeeText($this->article3->title);
        $res->assertDontSeeText($this->article4->title);
    }

    public function test_非公開()
    {
        $this->article1->update(['status' => 'private']);

        $url = route('category.pak.addon', [$this->pak->slug, $this->addon->slug]);
        $res = $this->get($url);
        $res->assertOk();
        $res->assertDontSeeText($this->article1->title);
    }
}
