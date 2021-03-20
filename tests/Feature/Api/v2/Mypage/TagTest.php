<?php

namespace Tests\Feature\Api\v2\Mypage;

use App\Models\Tag;
use Closure;
use Tests\TestCase;

class TagTest extends TestCase
{
    private Tag $tag1;
    private Tag $tag2;

    public function setUp(): void
    {
        parent::setUp();
        $this->tag1 = Tag::factory()->create(['name' => 'long tag name']);
        $this->tag2 = Tag::factory()->create(['name' => 'short']);
    }

    public function testIndex()
    {
        $url = route('api.v2.tags.search');

        $res = $this->getJson($url);
        $res->assertUnauthorized();

        $this->actingAs($this->user);

        $res = $this->getJson($url);
        $res->assertOK();
        $res->assertExactJson([$this->tag1->name, $this->tag2->name]);

        $url = route('api.v2.tags.search', ['name' => 'sh']);
        $res = $this->getJson($url);
        $res->assertOK();
        $res->assertExactJson([$this->tag2->name]);

        $url = route('api.v2.tags.search', ['name' => 'or']);
        $res = $this->getJson($url);
        $res->assertOK();
        $res->assertExactJson([$this->tag2->name]);

        $url = route('api.v2.tags.search', ['name' => 'rt']);
        $res = $this->getJson($url);
        $res->assertOK();
        $res->assertExactJson([$this->tag2->name]);
    }

    /**
     * @dataProvider dataValidation
     */
    public function testStore(Closure $data, ?string $error_field)
    {
        $url = route('api.v2.tags.store');

        $res = $this->postJson($url);
        $res->assertUnauthorized();

        $this->actingAs($this->user);
        $res = $this->postJson($url, Closure::bind($data, $this)());
        if (is_null($error_field)) {
            $res->assertCreated();
        } else {
            $res->assertJsonValidationErrors($error_field);
        }
    }

    public function dataValidation()
    {
        yield 'nameがnull' => [fn () => ['name' => null], 'name'];
        yield 'nameが21文字以上' => [fn () => ['name' => str_repeat('a', 21)], 'name'];
        yield 'nameが存在する' => [fn () => ['name' => $this->tag1->name], 'name'];
        yield '成功' => [fn () => ['name' => 'new_tag'], null];
    }
}
