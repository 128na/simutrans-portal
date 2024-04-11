<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\TagController;

use App\Models\Tag;
use App\Models\User;
use Tests\Feature\TestCase;

final class IndexTest extends TestCase
{
    private User $user;

    private Tag $tag;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->tag = Tag::factory()->create();
    }

    public function test(): void
    {
        $url = '/api/mypage/tags';
        $this->actingAs($this->user);

        $res = $this->getJson($url);
        $res->assertOK();
        $res->assertExactJson(['data' => [
            ['id' => $this->tag->id, 'name' => $this->tag->name, 'description' => $this->tag->description],
        ]]);
    }

    public function test_未ログイン(): void
    {
        $url = '/api/mypage/tags';

        $res = $this->getJson($url);
        $res->assertUnauthorized();
    }
}
