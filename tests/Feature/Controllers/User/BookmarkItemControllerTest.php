<?php

namespace Tests\Feature\Controllers\User;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\User\Bookmark;
use Tests\TestCase;

class BookmarkItemControllerTest extends TestCase
{
    public function test未ログイン()
    {
        $url = route('bookmarkItems.store');
        $response = $this->post($url);
        $response->assertRedirect(route('verification.notice'));
    }

    public function testメール未認証()
    {
        $this->user->update(['email_verified_at' => null]);
        $bookmark = Bookmark::factory()->create(['user_id' => $this->user->id]);
        $article = Article::factory()->create();
        $data = ['bookmarkItem' => [
            'bookmark_id' => $bookmark->id,
            'bookmark_itemable_type' => Article::class,
            'bookmark_itemable_id' => $article->id,
        ]];
        $url = route('bookmarkItems.store');
        $response = $this->post($url);
        $response->assertRedirect(route('verification.notice'));
    }

    public function test他人のブックマークには追加できない()
    {
        $bookmark = Bookmark::factory()->create();
        $data = ['bookmarkItem' => [
            'bookmark_id' => $bookmark->id,
            'bookmark_itemable_type' => Article::class,
            'bookmark_itemable_id' => Article::factory()->create()->id,
        ]];

        $url = route('bookmarkItems.store');
        $this->actingAs($this->user);
        $response = $this->post($url, $data);
        $response->assertStatus(403);
    }

    public function testログイン済み()
    {
        $bookmark = Bookmark::factory()->create(['user_id' => $this->user->id]);
        $article = Article::factory()->create();
        $data = ['bookmarkItem' => [
            'bookmark_id' => $bookmark->id,
            'bookmark_itemable_type' => Article::class,
            'bookmark_itemable_id' => $article->id,
        ]];

        $this->assertDatabaseMissing('bookmark_items', [
            'bookmark_id' => $bookmark->id,
            'bookmark_itemable_type' => Article::class,
            'bookmark_itemable_id' => $article->id,
        ]);

        $url = route('bookmarkItems.store');
        $this->actingAs($this->user);
        $response = $this->post($url, $data);
        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('bookmark_items', [
            'bookmark_id' => $bookmark->id,
            'bookmark_itemable_type' => Article::class,
            'bookmark_itemable_id' => $article->id,
        ]);
    }

    public function test追加済みアイテムはエラー()
    {
        $bookmark = Bookmark::factory()->create(['user_id' => $this->user->id]);
        $article = Article::factory()->create();
        $bookmark->bookmarkItems()->create([
            'bookmark_itemable_type' => Article::class,
            'bookmark_itemable_id' => $article->id,
        ]);

        $data = ['bookmarkItem' => [
            'bookmark_id' => $bookmark->id,
            'bookmark_itemable_type' => Article::class,
            'bookmark_itemable_id' => $article->id,
        ]];

        $url = route('bookmarkItems.store');

        $this->actingAs($this->user);
        $response = $this->post($url, $data);
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /**
     * @dataProvider dataValidation
     */
    public function testValidation(array $data, string $expectedError)
    {
        $bookmark = Bookmark::factory()->create(['user_id' => $this->user->id]);
        $itemable = $data['bookmark_itemable_type']::factory()->create();
        $data = ['bookmarkItem' => array_merge([
            'bookmark_itemable_id' => $itemable->id,
        ], $data)];

        $url = route('bookmarkItems.store');
        $this->actingAs($this->user);
        $response = $this->post($url, $data);
        $response->assertSessionHasErrors($expectedError);
    }

    public function dataValidation()
    {
        yield '不正なbookmark_id' => [
            ['bookmark_id' => 0, 'bookmark_itemable_type' => Article::class], 'bookmarkItem.bookmark_id',
        ];
        yield '不正なbookmark_itemable_type' => [
            ['bookmark_itemable_type' => Attachment::class], 'bookmarkItem.bookmark_itemable_type',
        ];
        yield '存在しないbookmark_itemable_id' => [
            ['bookmark_itemable_type' => Article::class, 'bookmark_itemable_id' => 0], 'bookmarkItem.bookmark_itemable_id',
        ];
        yield 'memoが1001文字以上' => [
            ['bookmark_itemable_type' => Article::class, 'memo' => str_repeat('a', 1001)], 'bookmarkItem.memo',
        ];
    }
}
