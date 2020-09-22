<?php

namespace Tests\Feature\Api\v2\Mypage;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function testIndex()
    {
        $user = User::factory()->create();
        $tag1 = Tag::factory()->create(['name' => 'long tag name']);
        $tag2 = Tag::factory()->create(['name' => 'short']);
        $url = route('api.v2.tags.search');

        $res = $this->getJson($url);
        $res->assertUnauthorized();

        $this->actingAs($user);

        $res = $this->getJson($url);
        $res->assertOK();
        $res->assertExactJson([$tag1->name, $tag2->name]);

        $url = route('api.v2.tags.search', ['name' => 'sh']);
        $res = $this->getJson($url);
        $res->assertOK();
        $res->assertExactJson([$tag2->name]);

        $url = route('api.v2.tags.search', ['name' => 'or']);
        $res = $this->getJson($url);
        $res->assertOK();
        $res->assertExactJson([$tag2->name]);

        $url = route('api.v2.tags.search', ['name' => 'rt']);
        $res = $this->getJson($url);
        $res->assertOK();
        $res->assertExactJson([$tag2->name]);
    }

    public function testStore()
    {
        $user = User::factory()->create();
        $tag1 = Tag::factory()->create(['name' => 'long tag name']);
        $tag2_name = 'short';
        $url = route('api.v2.tags.store');

        $res = $this->postJson($url);
        $res->assertUnauthorized();

        $this->actingAs($user);

        $res = $this->postJson($url, ['name' => null]);
        $res->assertJsonValidationErrors(['name']);
        $res = $this->postJson($url, ['name' => str_repeat('a', 21)]);
        $res->assertJsonValidationErrors(['name']);
        $res = $this->postJson($url, ['name' => $tag1->name]);
        $res->assertJsonValidationErrors(['name']);
        $res = $this->postJson($url, ['name' => $tag2_name]);
        $res->assertOK();
        $res->assertExactJson([$tag1->name, $tag2_name]);
    }
}
