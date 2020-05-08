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

        // 記事に紐づいていないタグ 表示されないこと
        $res = $this->get($url);
        $res->assertOk();
        $res->assertDontSee($tag->name);

        // 公開されている記事に紐づいてるタグ 表示されること
        $article->tags()->sync([$tag->id]);
        $res = $this->get($url);
        $res->assertOk();
        $res->assertSee($tag->name);

        // 非公開の記事に紐づいてるタグ 表示されないこと
        $article->update(['status' => 'draft']);
        $res = $this->get($url);
        $res->assertOk();
        $res->assertDontSee($tag->name);
    }
}
