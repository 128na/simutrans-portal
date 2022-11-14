<?php

namespace Tests\Feature\Controllers\Api\v2\Mypage;

use App\Models\Tag;
use Closure;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    private Tag $tag1;
    private Tag $tag2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tag1 = Tag::factory()->create(['name' => 'long tag name', 'description' => 'desc1']);
        $this->tag2 = Tag::factory()->create(['name' => 'short', 'description' => 'desc2']);
    }

    public function testIndex()
    {
        $url = route('api.v2.tags.search');

        $res = $this->getJson($url);
        $res->assertUnauthorized();

        $this->actingAs($this->user);

        $res = $this->getJson($url);
        $res->assertOK();
        $res->assertExactJson(['data' => [
            ['id' => $this->tag1->id, 'name' => $this->tag1->name, 'description' => $this->tag1->description],
            ['id' => $this->tag2->id, 'name' => $this->tag2->name, 'description' => $this->tag2->description],
        ]]);

        $url = route('api.v2.tags.search', ['name' => 'sh']);
        $res = $this->getJson($url);
        $res->assertOK();
        $res->assertExactJson(['data' => [['id' => $this->tag2->id, 'name' => $this->tag2->name, 'description' => $this->tag2->description]]]);

        $url = route('api.v2.tags.search', ['name' => 'or']);
        $res = $this->getJson($url);
        $res->assertOK();
        $res->assertExactJson(['data' => [['id' => $this->tag2->id, 'name' => $this->tag2->name, 'description' => $this->tag2->description]]]);

        $url = route('api.v2.tags.search', ['name' => 'rt']);
        $res = $this->getJson($url);
        $res->assertOK();
        $res->assertExactJson(['data' => [['id' => $this->tag2->id, 'name' => $this->tag2->name, 'description' => $this->tag2->description]]]);

        $url = route('api.v2.tags.search', ['name' => 'desc2']);
        $res = $this->getJson($url);
        $res->assertOK();
        $res->assertExactJson(['data' => [['id' => $this->tag2->id, 'name' => $this->tag2->name, 'description' => $this->tag2->description]]]);
    }

    public function testStore認証()
    {
        $url = route('api.v2.tags.store');

        // 未ログインは401
        $res = $this->postJson($url);
        $res->assertUnauthorized();

        // メール未認証は403
        $this->user->update(['email_verified_at' => null]);
        $this->actingAs($this->user);
        $res = $this->postJson($url);
        $res->assertForbidden();
    }

    /**
     * @dataProvider dataValidation
     */
    public function testStore(Closure $data, ?string $error_field)
    {
        $url = route('api.v2.tags.store');

        $this->actingAs($this->user);
        $params = Closure::bind($data, $this)();
        $res = $this->postJson($url, $params);
        if (is_null($error_field)) {
            $res->assertCreated();
            $this->assertDatabaseHas('tags', ['name' => $params['name'], 'created_by' => $this->user->id]);
        } else {
            $res->assertJsonValidationErrors($error_field);
        }
    }

    public function testUpdate認証()
    {
        $tag = Tag::factory()->create();
        $url = route('api.v2.tags.update', $tag);

        // 未ログインは401
        $res = $this->postJson($url);
        $res->assertUnauthorized();

        // メール未認証は403
        $this->user->update(['email_verified_at' => null]);
        $this->actingAs($this->user);
        $res = $this->postJson($url);
        $res->assertForbidden();
    }

    public function dataValidation()
    {
        yield 'nameがnull' => [fn () => ['name' => null], 'name'];
        yield 'nameが21文字以上' => [fn () => ['name' => str_repeat('a', 21)], 'name'];
        yield 'nameが存在する' => [fn () => ['name' => $this->tag1->name], 'name'];
        yield '成功' => [fn () => ['name' => 'new_tag'], null];
    }

    /**
     * @dataProvider dataUpdateValidation
     */
    public function testUpdate(Closure $data, ?string $error_field)
    {
        $tag = Tag::factory()->create();
        $url = route('api.v2.tags.update', $tag);

        $this->actingAs($this->user);
        $params = Closure::bind($data, $this)();
        $res = $this->postJson($url, $params);
        if (is_null($error_field)) {
            $res->assertOk();
            $this->assertDatabaseHas('tags', [
                'id' => $tag->id,
                'last_modified_by' => $this->user->id,
                'description' => $params['description'],
            ]);
        } else {
            $res->assertJsonValidationErrors($error_field);
        }
    }

    public function dataUpdateValidation()
    {
        yield 'descriptionが1024文字以下' => [fn () => ['description' => str_repeat('a', 1024)], null];
        yield 'descriptionが1025文字以上' => [fn () => ['description' => str_repeat('a', 1025)], 'description'];
    }

    public function testUpdate編集ロック()
    {
        $tag = Tag::factory()->create(['editable' => false]);
        $url = route('api.v2.tags.update', $tag);

        $this->actingAs($this->user);
        $data = ['description' => 'dummy'];
        $res = $this->postJson($url, $data);
        $res->assertForbidden();
    }
}
