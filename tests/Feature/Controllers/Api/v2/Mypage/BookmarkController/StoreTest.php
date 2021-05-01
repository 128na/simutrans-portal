<?php

namespace Tests\Feature\Controllers\Api\v2\Mypage\BookmarkController;

use Tests\TestCase;

class StoreTest extends TestCase
{
    public function test未ログイン()
    {
        $url = route('api.v2.bookmarks.store');
        $response = $this->postJson($url);
        $response->assertStatus(401);
    }

    public function testメール未認証()
    {
        $this->user->update(['email_verified_at' => null]);
        $this->actingAs($this->user);

        $url = route('api.v2.bookmarks.store');
        $response = $this->postJson($url);
        $response->assertStatus(403);
    }

    public function testOK()
    {
        $this->actingAs($this->user);
        $url = route('api.v2.bookmarks.store');

        $data = [
            'bookmark' => [
                'title' => 'hoge',
            ],
        ];
        $response = $this->postJson($url, $data);
        $response->assertOk();

        $this->assertDatabaseHas('bookmarks', [
            'title' => 'hoge',
            'description' => null,
            'is_public' => false,
        ]);
    }

    /**
     * @dataProvider dataValidate
     */
    public function testValidate(array $dataBookmark, string $expectedError)
    {
        $this->actingAs($this->user);

        $url = route('api.v2.bookmarks.store');
        $response = $this->postJson($url, ['bookmark' => $dataBookmark]);
        $response->assertJsonValidationErrors($expectedError);
    }

    public function dataValidate()
    {
        yield 'bookmark.titleが空' => [
            ['title' => null],
            'bookmark.title',
        ];
        yield 'bookmark.titleが256文字以上' => [
            ['title' => str_repeat('a', 256)],
            'bookmark.title',
        ];
        yield 'bookmark.titleが文字以外' => [
            ['title' => []],
            'bookmark.title',
        ];
        yield 'bookmark.descriptionが1001文字以上' => [
            ['description' => str_repeat('a', 1001)],
            'bookmark.description',
        ];
        yield 'bookmark.descriptionが文字以外' => [
            ['description' => []],
            'bookmark.description',
        ];
        yield 'bookmark.is_publicがbook以外' => [
            ['is_public' => 'hoge'],
            'bookmark.is_public',
        ];
    }
}
