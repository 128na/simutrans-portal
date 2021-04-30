<?php

namespace Tests\Feature\Controllers\Front\ArticleController;

use App\Models\Article;
use App\Models\Category;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    private Article $article1;
    private Article $article2;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article1 = Article::factory()->publish()->addonIntroduction()->create();
        $this->article2 = Article::factory()->publish()->addonIntroduction()->create();
        $this->category = Category::addon()->inRandomOrder()->first();
        $this->article1->categories()->save($this->category);
    }

    public function test()
    {
        $url = route('category', [$this->category->type, $this->category->slug]);
        $res = $this->get($url);
        $res->assertOk();
        $res->assertSeeText($this->article1->title);
        $res->assertDontSeeText($this->article2->title);
    }

    public function test_非公開()
    {
        $this->article1->update(['status' => 'private']);

        $url = route('category', [$this->category->type, $this->category->slug]);
        $res = $this->get($url);
        $res->assertOk();
        $res->assertDontSeeText($this->article1->title);
    }
}
