<?php

namespace Tests\Feature\Api\v2\Article;

use App\Models\Tag;
use Tests\TestCase;

class TagTest extends TestCase
{
    /**
     * @dataProvider dataStatus
     */
    public function testShow(string $status, bool $should_see)
    {
        $this->article->fill(['status' => $status])->save();
        $tag = Tag::factory()->create();
        $this->article->tags()->attach($tag->id);

        $url = route('api.v2.articles.tag', $tag);

        $res = $this->getJson($url);
        $res->assertStatus(200);

        if ($should_see) {
            $res->assertJsonFragment(['title' => $this->article->title]);
        } else {
            $res->assertJsonMissing(['title' => $this->article->title]);
        }
    }

    public function testInvalid()
    {
        $url = route('api.v2.articles.tag', 65535);

        $res = $this->getJson($url);
        $res->assertStatus(404);
    }
}
