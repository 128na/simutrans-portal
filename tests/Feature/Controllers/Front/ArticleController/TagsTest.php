<?php

namespace Tests\Feature\Controllers\Front\ArticleController;

use App\Models\Article;
use App\Models\Tag;
use Tests\TestCase;

class TagsTest extends TestCase
{
    private Article $article1;
    private Article $article2;
    private Tag $tag1;
    private Tag $tag2;
    private Tag $tag3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->article1 = Article::factory()->publish()->addonIntroduction()->create();
        $this->tag1 = Tag::factory()->create(['name' => 'tag1']);
        $this->article1->tags()->save($this->tag1);

        $this->article2 = Article::factory()->draft()->addonPost()->create();
        $this->tag2 = Tag::factory()->create(['name' => 'tag2']);
        $this->article2->tags()->save($this->tag2);

        $this->tag3 = Tag::factory()->create(['name' => 'tag3']);
    }

    public function test()
    {
        $url = route('tags');
        $res = $this->get($url);
        $res->assertOk();
        $res->assertSeeText($this->tag1->name);
        $res->assertDontSeeText($this->tag2->name);
        $res->assertDontSeeText($this->tag3->name);
    }
}
