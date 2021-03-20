<?php

namespace Tests\Feature\Api\v2\Article;

use App\Models\Category;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    /**
     * @dataProvider dataStatus
     */
    public function testShow(string $status, bool $should_see)
    {
        $this->article->fill(['status' => $status])->save();
        $category = Category::first();
        $this->article->categories()->attach($category->id);

        $url = route('api.v2.articles.category', $category);

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
        $url = route('api.v2.articles.category', 65535);

        $res = $this->getJson($url);
        $res->assertStatus(404);
    }
}
