<?php

namespace Tests\Feature\Front;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTagsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    /**
     *  表示
     **/
    public function testShow()
    {
        $url = route('tags');
        $res = $this->get($url);
        $res->assertOk();
    }

    /**
     *  タグ名
     **/
    public function testTagDoesntHaveArticles()
    {
        $url = route('tags');
        $tag = factory(Tag::class)->create();
        $user = factory(User::class)->create();
        $article = $this->createAddonIntroduction($user);

        // assertSeeではキャッシュされたgzipがテキストにパースされないのでログインしておく
        $this->actingAs($user);

        $res = $this->get($url);
        $res->assertOk();
        $res->assertDontSee($tag->name);

        $article->tags()->sync([$tag->id]);

        $res = $this->get($url);
        $res->assertOk();
        $res->assertSee($tag->name);
    }
}
