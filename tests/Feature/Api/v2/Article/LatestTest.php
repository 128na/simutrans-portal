<?php

namespace Tests\Feature\Api\v2\Article;

use App\Models\Category;
use Tests\TestCase;

class LatestTest extends TestCase
{
    /**
     * @dataProvider dataStatus
     */
    public function testShow(string $status, bool $should_see)
    {
        $this->article->fill(['status' => $status])->save();

        $url = route('api.v2.articles.latest');

        $res = $this->getJson($url);
        $res->assertStatus(200);

        if ($should_see) {
            $res->assertJsonFragment(['title' => $this->article->title]);
        } else {
            $res->assertJsonMissing(['title' => $this->article->title]);
        }
    }

    public function testThrottle()
    {
        $url = route('api.v2.articles.latest');

        $throttle_limit = 100;
        for ($i = 0; $i < $throttle_limit; ++$i) {
            $res = $this->getJson($url);
            $res->assertStatus(200);
        }
        $res = $this->getJson($url);
        $res->assertStatus(429);
    }

    public function testData()
    {
        $this->article = $this->createAddonPost();
        $category = Category::first();
        $this->article->categories()->attach($category->id);

        $url = route('api.v2.articles.latest');

        $res = $this->getJson($url);
        $res->assertStatus(200);

        $res->assertJsonPath('data.0.id', $this->article->id);
        $res->assertJsonPath('data.0.title', $this->article->title);
        $res->assertJsonPath('data.0.post_type', $this->article->post_type);
        $res->assertJsonPath('data.0.contents', $this->article->contents->getDescription());
        $res->assertJsonPath('data.0.url', route('articles.show', $this->article->slug));
        $res->assertJsonPath('data.0.author', $this->article->contents->author);
        $res->assertJsonPath('data.0.categories.0.slug', $category->slug);
        $res->assertJsonPath('data.0.categories.0.type', $category->type);
        $res->assertJsonPath('data.0.categories.0.url', route('category', [$category->type, $category->slug]));
        $res->assertJsonPath('data.0.categories.0.api', route('api.v2.articles.category', [$category->id]));
        $res->assertJsonPath('data.0.created_by.name', $this->user->name);
        $res->assertJsonPath('data.0.created_by.url', route('user', $this->user->id));
        $res->assertJsonPath('data.0.created_by.api', route('api.v2.articles.user', $this->user->id));
    }
}
