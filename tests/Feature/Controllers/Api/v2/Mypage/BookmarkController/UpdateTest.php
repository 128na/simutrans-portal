<?php

namespace Tests\Feature\Controllers\Api\v2\Mypage\BookmarkController;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Models\User\Bookmark;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    private Bookmark $bookmark;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookmark = Bookmark::factory()->create(['user_id' => $this->user->id]);
    }

    public function test未ログイン()
    {
        $url = route('api.v2.bookmarks.update', $this->bookmark);
        $response = $this->postJson($url);
        $response->assertStatus(401);
    }

    public function testメール未認証()
    {
        $this->user->update(['email_verified_at' => null]);
        $this->actingAs($this->user);

        $url = route('api.v2.bookmarks.update', $this->bookmark);
        $response = $this->postJson($url);
        $response->assertStatus(403);
    }

    public function test他人のはNG()
    {
        $this->actingAs(User::factory()->create());

        $url = route('api.v2.bookmarks.update', $this->bookmark);
        $data = [
            'bookmark' => ['title' => 'hoge', 'is_public' => false],
            'bookmarkItems' => [],
        ];
        $response = $this->postJson($url, $data);
        $response->assertStatus(403);
    }

    public function testOK()
    {
        $this->actingAs($this->user);

        $url = route('api.v2.bookmarks.update', $this->bookmark);
        $data = [
            'bookmark' => ['title' => 'hoge', 'is_public' => false],
            'bookmarkItems' => [],
        ];
        $response = $this->postJson($url, $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $this->bookmark->user_id,
            'uuid' => $this->bookmark->uuid,
            'title' => 'hoge',
            'description' => $this->bookmark->description,
            'is_public' => false,
        ]);
    }

    /**
     * @dataProvider dataValidate
     */
    public function testValidate(array $dataBookmark, array $dataBookmarkItem, string $expectedError)
    {
        $this->actingAs($this->user);

        $url = route('api.v2.bookmarks.update', $this->bookmark);
        $data = [
            'bookmark' => $dataBookmark,
            'bookmarkItems' => [$dataBookmarkItem],
        ];
        $response = $this->postJson($url, $data);
        $response->assertJsonValidationErrors($expectedError);
    }

    public function dataValidate()
    {
        yield 'bookmark.titleが空' => [
            ['title' => null],
            [],
            'bookmark.title',
        ];
        yield 'bookmark.titleが256文字以上' => [
            ['title' => str_repeat('a', 256)],
            [],
            'bookmark.title',
        ];
        yield 'bookmark.titleが文字以外' => [
            ['title' => []],
            [],
            'bookmark.title',
        ];
        yield 'bookmark.descriptionが1001文字以上' => [
            ['description' => str_repeat('a', 1001)],
            [],
            'bookmark.description',
        ];
        yield 'bookmark.descriptionが文字以外' => [
            ['description' => []],
            [],
            'bookmark.description',
        ];
        yield 'bookmark.is_publicがbook以外' => [
            ['is_public' => 'hoge'],
            [],
            'bookmark.is_public',
        ];
        yield 'bookmarkItems.0.bookmark_itemable_typeが空' => [
            ['title' => 'a'],
            ['bookmark_itemable_type' => null],
            'bookmarkItems.0.bookmark_itemable_type',
        ];
        yield 'bookmarkItems.0.bookmark_itemable_typeが指定クラス以外' => [
            ['title' => 'a'],
            ['bookmark_itemable_type' => Attachment::class],
            'bookmarkItems.0.bookmark_itemable_type',
        ];
        yield 'bookmarkItems.0.bookmark_itemable_idが空' => [
            ['title' => 'a'],
            ['bookmark_itemable_type' => Article::class, 'bookmark_itemable_id' => null],
            'bookmarkItems.0.bookmark_itemable_id',
        ];
        yield 'bookmarkItems.0.bookmark_itemable_idが存在しない(Article)' => [
            ['title' => 'a'],
            ['bookmark_itemable_type' => Article::class, 'bookmark_itemable_id' => 0],
            'bookmarkItems.0.bookmark_itemable_id',
        ];
        yield 'bookmarkItems.0.bookmark_itemable_idが存在しない(Bookmark)' => [
            ['title' => 'a'],
            ['bookmark_itemable_type' => Bookmark::class, 'bookmark_itemable_id' => 0],
            'bookmarkItems.0.bookmark_itemable_id',
        ];
        yield 'bookmarkItems.0.bookmark_itemable_idが存在しない(Category)' => [
            ['title' => 'a'],
            ['bookmark_itemable_type' => Category::class, 'bookmark_itemable_id' => 0],
            'bookmarkItems.0.bookmark_itemable_id',
        ];
        yield 'bookmarkItems.0.bookmark_itemable_idが存在しない(Tag)' => [
            ['title' => 'a'],
            ['bookmark_itemable_type' => Tag::class, 'bookmark_itemable_id' => 0],
            'bookmarkItems.0.bookmark_itemable_id',
        ];
        yield 'bookmarkItems.0.bookmark_itemable_idが存在しない(User)' => [
            ['title' => 'a'],
            ['bookmark_itemable_type' => User::class, 'bookmark_itemable_id' => 0],
            'bookmarkItems.0.bookmark_itemable_id',
        ];
        yield 'bookmarkItems.0.memoが1001文字以上' => [
            ['title' => 'a'],
            ['bookmark_itemable_type' => Article::class, 'memo' => str_repeat('a', 1001)],
            'bookmarkItems.0.memo',
        ];
        yield 'bookmarkItems.0.orderが数値以外' => [
            ['title' => 'a'],
            ['bookmark_itemable_type' => Article::class, 'order' => 'hoge'],
            'bookmarkItems.0.order',
        ];
        yield 'bookmarkItems.0.orderが整数以外' => [
            ['title' => 'a'],
            ['bookmark_itemable_type' => Article::class, 'order' => 3.34],
            'bookmarkItems.0.order',
        ];
    }
}
