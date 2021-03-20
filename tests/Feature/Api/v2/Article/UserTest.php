<?php

namespace Tests\Feature\Api\v2\Article;

use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * @dataProvider dataStatus
     */
    public function testShow(string $status, bool $should_see)
    {
        $this->article->fill(['status' => $status])->save();
        $url = route('api.v2.articles.user', $this->user);

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
        $url = route('api.v2.articles.user', 65535);

        $res = $this->getJson($url);
        $res->assertStatus(404);
    }
}
