<?php

namespace Tests\Feature\Front;

use App\Models\Tag;
use Closure;
use Tests\TestCase;

class ShowTagsTest extends TestCase
{
    private Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tag = Tag::factory()->create();
    }

    public function test_タグ一覧()
    {
        $url = route('tags');
        $res = $this->get($url);
        $res->assertOk();
    }

    /**
     * @dataProvider data
     */
    public function test_記事の無いタグ(Closure $fn, bool $should_see)
    {
        // assertSeeではキャッシュされたgzipがテキストにパースされないのでログインしておく
        $this->actingAs($this->user);

        Closure::bind($fn, $this)();

        // 記事に紐づいていないタグ 表示されないこと
        $url = route('tags');
        $res = $this->get($url);
        $res->assertOk();
        if ($should_see) {
            $res->assertSee($this->tag->name);
        } else {
            $res->assertDontSee($this->tag->name);
        }
    }

    public function data()
    {
        yield '記事に紐づいていないタグ' => [fn () => null, false];
        yield '公開されている記事に紐づいてるタグ' => [fn () => $this->article->tags()->sync([$this->tag->id]), true];
        yield '非公開の記事に紐づいてるタグ' => [function () {
            $this->article->tags()->sync([$this->tag->id]);
            $this->article->update(['status' => 'draft']);
        }, false];
    }
}
