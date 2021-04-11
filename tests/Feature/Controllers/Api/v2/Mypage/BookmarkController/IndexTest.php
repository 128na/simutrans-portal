<?php

namespace Tests\Feature\Controllers\Api\v2\Mypage\BookmarkController;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Models\User\Bookmark;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function test未ログイン()
    {
        $url = route('api.v2.bookmarks.index');
        $response = $this->getJson($url);
        $response->assertStatus(401);
    }

    public function testメール未認証()
    {
        $this->user->update(['email_verified_at' => null]);
        $this->actingAs($this->user);

        $url = route('api.v2.bookmarks.index');
        $response = $this->getJson($url);
        $response->assertStatus(403);
    }

    public function testOK()
    {
        $this->actingAs($this->user);

        $url = route('api.v2.bookmarks.index');
        $response = $this->getJson($url);
        $response->assertOk();
    }

    public function testResponse()
    {
        Bookmark::query()->delete();
        $bookmark = Bookmark::factory()->create(['user_id' => $this->user->id]);

        $article = Article::factory()->create();
        $article->bookmarkItemables()->create(['bookmark_id' => $bookmark->id, 'order' => 10]);

        $bookmark2 = Bookmark::factory()->create();
        $bookmark2->bookmarkItemables()->create(['bookmark_id' => $bookmark->id, 'order' => 8]);

        $category = Category::inRandomOrder()->first();
        $category->bookmarkItemables()->create(['bookmark_id' => $bookmark->id, 'order' => 6]);

        $tag = Tag::factory()->create();
        $tag->bookmarkItemables()->create(['bookmark_id' => $bookmark->id, 'order' => 4]);

        $user = User::factory()->create();
        $user->bookmarkItemables()->create(['bookmark_id' => $bookmark->id, 'order' => 2]);

        $this->actingAs($this->user);

        $url = route('api.v2.bookmarks.index');
        $response = $this->getJson($url);
        $response->assertOk();
        $response->assertJson(['data' => [
            [
                'id' => $bookmark->id,
                'uuid' => $bookmark->uuid,
                'title' => $bookmark->title,
                'description' => $bookmark->description,
                'is_public' => (bool) $bookmark->is_public,
                'created_at' => $bookmark->created_at->toDateTimeString(),
                'bookmarkItems' => [
                    [
                        'bookmark_itemable_type' => User::class,
                        'bookmark_itemable_id' => $user->id,
                        'memo' => $user->memo,
                        'order' => 2,
                        'title' => $user->name,
                    ],
                    [
                        'bookmark_itemable_type' => Tag::class,
                        'bookmark_itemable_id' => $tag->id,
                        'memo' => $tag->memo,
                        'order' => 4,
                        'title' => $tag->name,
                    ],
                    [
                        'bookmark_itemable_type' => Category::class,
                        'bookmark_itemable_id' => $category->id,
                        'memo' => $category->memo,
                        'order' => 6,
                        'title' => __("category.{$category->type}.{$category->slug}"),
                    ],
                    [
                        'bookmark_itemable_type' => Bookmark::class,
                        'bookmark_itemable_id' => $bookmark2->id,
                        'memo' => $bookmark2->memo,
                        'order' => 8,
                        'title' => $bookmark2->title,
                    ],
                    [
                        'bookmark_itemable_type' => Article::class,
                        'bookmark_itemable_id' => $article->id,
                        'memo' => $article->memo,
                        'order' => 10,
                        'title' => $article->title,
                    ],
                ],
            ],
        ]]);
    }
}
